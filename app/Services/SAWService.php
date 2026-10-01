<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\EarlyWarningResult;
use App\Models\HasilUjian;
use App\Models\Jadwal;
use App\Models\KehadiranGuru;
use App\Models\Kelas;
use App\Models\NilaiTugas;
use App\Models\PerilakuSiswa;
use App\Models\Semester;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SAWService
{
    private const HARI_INDONESIA = [
        1 => 'senin',
        2 => 'selasa',
        3 => 'rabu',
        4 => 'kamis',
        5 => 'jumat',
        6 => 'sabtu',
        0 => 'minggu',
    ];

    public function generateHarian(?string $tanggalHitung = null)
    {
        $semesterAktif = Semester::aktif();
        if (! $semesterAktif) {
            return;
        }

        $tanggalHitung ??= Carbon::today()->toDateString();
        Kelas::all()->each(function (Kelas $kelas) use ($tanggalHitung, $semesterAktif) {
            $this->generateUntukKelas($kelas->id, $semesterAktif, $tanggalHitung);
        });
    }

    public function generateUntukKelas(int $kelasId, Semester $semester, string $tanggalHitung)
    {
        $kelas = Kelas::findOrFail($kelasId);
        $siswas = $kelas->siswas;

        if ($siswas->isEmpty()) {
            return;
        }

        $mentahPersiswa = $siswas->mapWithKeys(function (Siswa $siswa) use ($semester, $tanggalHitung) {
            return [$siswa->id => $this->hitungNilaiMentah($siswa, $semester, $tanggalHitung)];
        });

        $hasilPerSiswa = $this->prosesSaw($mentahPersiswa);
        $metaDataKelas = $this->cekInputMetaData($kelas, $tanggalHitung, $semester);
        foreach ($siswas as $siswa) {
            $this->simpanHasil(
                siswa: $siswa,
                kelas: $kelas,
                semester: $semester,
                tanggalHitung: $tanggalHitung,
                mentah: $mentahPersiswa[$siswa->id],
                hasil: $hasilPerSiswa[$siswa->id],
                metadataKelas: $metaDataKelas
            );
        }
    }

    private function hitungNilaiMentah(Siswa $siswa, Semester $semester, string $tanggalHitung)
    {
        $perilaku = $this->hitungPerilaku($siswa, $semester);

        return [
            'c1' => $this->hitungAkademik($siswa, $semester),
            'c2' => $this->hitungAbsensi($siswa, $semester),
            'c3' => $perilaku['c3'],
            'total_negatif' => $perilaku['total_negatif'],
            'total_positif' => $perilaku['total_positif'],
        ];
    }

    private function hitungAkademik(Siswa $siswa, Semester $semester)
    {
        $rataTugas = NilaiTugas::whereHas('tugas', fn ($q) => $q->where('semester_id', $semester->id))
            ->where('siswa_id', $siswa->id)->where('status', 'selesai')->avg('nilai');
        $rataUjian = HasilUjian::whereHas('ujianHarian', fn ($q) => $q->where('semester_id', $semester->id))
            ->where('siswa_id', $siswa->id)->where('status', 'selesai')->avg('nilai');
        if ($rataTugas === null && $rataUjian === null) {
            return 0;
        }

        if ($rataTugas === null) {
            return round((float) $rataUjian, 2);
        }

        if ($rataUjian === null) {
            return round((float) $rataTugas, 2);
        }

        return round(((float) $rataTugas + (float) $rataUjian) / 2, 3);
    }

    private function hitungAbsensi(Siswa $siswa, Semester $semester)
    {
        return (float) Absensi::where('siswa_id', $siswa->id)
            ->where('tipe', 'mapel')->whereIn('status', ['izin', 'sakit', 'alpha'])
            ->whereHas('jadwal', fn ($q) => $q->where('semester_id', $semester->id))->count();
    }

    private function hitungPerilaku(Siswa $siswa, Semester $semester): array
    {
        $rentang = [$semester->tanggal_mulai, $semester->tanggal_selesai];

        $totalNegatif = (float) PerilakuSiswa::where('siswa_id', $siswa->id)
            ->whereBetween('tanggal', $rentang)
            ->join('perilakus', 'perilaku_siswas.perilaku_id', '=', 'perilakus.id')
            ->where('perilakus.jenis', 'negatif')
            ->sum('perilakus.poin');

        $totalPositif = (float) PerilakuSiswa::where('siswa_id', $siswa->id)
            ->whereBetween('tanggal', $rentang)
            ->join('perilakus', 'perilaku_siswas.perilaku_id', '=', 'perilakus.id')
            ->where('perilakus.jenis', 'positif')
            ->sum('perilakus.poin');

        return [
            'c3' => max(0, 100 - $totalNegatif),
            'total_negatif' => $totalNegatif,
            'total_positif' => $totalPositif,
        ];
    }

    private function prosesSaw(Collection $mentahPerSiswa)
    {
        $maxC1 = $mentahPerSiswa->max('c1') ?: 1;
        $maxC3 = $mentahPerSiswa->max('c3') ?: 1;
        $minC2 = $mentahPerSiswa->min('c2');

        $bobot = config('ews.bobot');

        return $mentahPerSiswa->map(function (array $mentah) use ($maxC1, $maxC3, $minC2, $bobot) {
            $r1 = round($mentah['c1'] / $maxC1, 4);
            $r2 = round($minC2 / ($mentah['c2'] + 1), 4);
            $r3 = round($mentah['c3'] / $maxC3, 4);

            $skorAkhir = round(
                ($r1 * $bobot['c1_akademik']) + ($r2 * $bobot['c2_absensi']) + ($r3 * $bobot['c3_perilaku']),
                4
            );

            return [
                'r1' => $r1,
                'r2' => $r2,
                'r3' => $r3,
                'skor_akhir' => $skorAkhir,
                'kategori' => $this->tentukanKategori($skorAkhir),
            ];
        });
    }

    private function tentukanKategori(float $skor)
    {
        $threshold = config('ews.threshold');

        return match (true) {
            $skor >= $threshold['aman'] => 'aman',
            $skor >= $threshold['perhatian'] => 'perhatian',
            default => 'binaan'
        };
    }

    private function cekInputMetaData(Kelas $kelas, string $tanggalHitung, Semester $semester)
    {
        $namaHari = self::HARI_INDONESIA[Carbon::parse($tanggalHitung)->dayOfWeek];
        $jadwalHariIni = Jadwal::with('guru', 'mapel')->where('kelas_id', $kelas->id)->where('semester_id', $semester->id)
            ->where('hari', $namaHari)->get();

        $guruTidakHadir = [];
        $absensiBelumDiinput = [];

        foreach ($jadwalHariIni as $jadwal) {
            $kehadiranGuru = KehadiranGuru::where('guru_id', $jadwal->guru_id)->where('tanggal', $tanggalHitung)->get();
            $hadir = $kehadiranGuru->firstWhere('status', 'hadir');

            if (! $hadir) {
                $recordTerakhir = $kehadiranGuru->first();
                $guruTidakHadir[] = [
                    'guru_id' => $jadwal->guru_id,
                    'guru_nama' => $jadwal->guru->nama,
                    'mapel' => $jadwal->mapel->nama,
                    'status' => $recordTerakhir?->status ?? 'tidak_ada_catatan',
                ];
            }

            $sudahInputAbsensi = Absensi::where('jadwal_id', $jadwal->id)->where('tanggal', $tanggalHitung)->exists();
            if (! $sudahInputAbsensi) {
                $absensiBelumDiinput[] = [
                    'guru_id' => $jadwal->guru_id,
                    'guru_nama' => $jadwal->guru->nama,
                    'mapel' => $jadwal->mapel->nama,
                    'status' => 'belum_diinput',
                ];
            }
        }

        $lengkap = empty($guruTidakHadir) && empty($absensiBelumDiinput);

        return [
            'lengkap' => $lengkap,
            'metadata' => $lengkap ? null : [
                'akademik' => $guruTidakHadir,
                'absensi' => $absensiBelumDiinput,
            ],
        ];
    }

    private function simpanHasil(
        Siswa $siswa,
        Kelas $kelas,
        Semester $semester,
        string $tanggalHitung,
        array $mentah,
        array $hasil,
        array $metadataKelas,
    ): EarlyWarningResult {
        return EarlyWarningResult::updateOrCreate(
            [
                'siswa_id' => $siswa->id,
                'semester_id' => $semester->id,
                'tanggal_hitung' => $tanggalHitung,
            ],
            [
                'kelas_id' => $kelas->id,
                'generated_by' => null,
                'generated_at' => now(),
                'c1_akademik' => $mentah['c1'],
                'c2_absensi' => $mentah['c2'],
                'c3_perilaku' => $mentah['c3'],
                'total_perilaku_negatif' => $mentah['total_negatif'],
                'total_perilaku_positif' => $mentah['total_positif'],
                'r1_absensi' => $hasil['r1'],
                'r2_perilaku' => $hasil['r2'],
                'r3_akademik' => $hasil['r3'],
                'skor_akhir' => $hasil['skor_akhir'],
                'kategori' => $hasil['kategori'],
                'data_tidak_lengkap' => ! $metadataKelas['lengkap'],
                'input_metadata' => $metadataKelas['metadata'],
            ]
        );
    }
}
