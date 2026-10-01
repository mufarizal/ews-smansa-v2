<?php

namespace App\Services;

use App\Models\HasilUjian;
use App\Models\JawabanUjian;
use App\Models\Siswa;
use App\Models\SoalUjian;
use App\Models\UjianHarian;

use function Symfony\Component\Clock\now;

class UjianService
{
    public function mulai(UjianHarian $ujian, Siswa $siswa)
    {
        return HasilUjian::firstOrCreate(
            ['ujian_harian_id' => $ujian->id, 'siswa_id' => $siswa->id],
            ['waktu_mulai' => now(), 'status' => 'sedang_dikerjakan']
        );
    }

    public function simpanJawaban(SoalUjian $soal, Siswa $siswa, HasilUjian $hasil, array $payload)
    {
        if ($hasil->status !== 'sedang_mengerjakan') {
            return 'Ujian sudah dikumpulkan, Jawaban tidak bisa diubah';
        }

        if ($hasil->sudahHabisWaktu()) {
            $this->submitAkhir($soal->ujianHarian, $siswa, $hasil, otomatis: true);

            return 'Waktu ujian sudah habis, Ujian otomatis dikumpulkan, Jawaban tidak bisa diubah';
        }

        $data = match ($soal->tipe_soal) {
            'pilihan_ganda' => [
                'jawaban_teks' => strtoupper($payload['jawaban_teks']),
                'poin_diperoleh' => strtoupper($payload['jawaban_teks']) === strtoupper($soal->kunci_jawaban) ? $soal->poin : 0,
            ],
            'esai' => [
                'jawaban_teks' => $payload['jawaban_teks'],
                'poin_diperoleh' => null,
            ]
        };

        JawabanUjian::updateOrCreate(
            ['soal_ujian_id' => $soal->id, 'siswa_id' => $siswa->id],
            $data
        );

        return null;
    }

    public function submitAkhir(UjianHarian $ujian, Siswa $siswa, HasilUjian $hasil, bool $otomatis = false)
    {
        if ($hasil->status !== 'sedang_mengerjakan') {
            return;
        }

        $hasil->update(['waktu_selesai' => now()]);
        $this->hitungUlangNilai($ujian, $siswa, $hasil);
    }

    public function hitungUlangNilai(UjianHarian $ujian, Siswa $siswa, HasilUjian $hasil)
    {
        $soalList = $ujian->soalUjians;
        $jawabanList = JawabanUjian::whereIn('soal_ujian_id', $soalList->pluck('id'))
            ->where('siswa_id', $siswa->id)->get()->keyBy('soal_ujian_id');
        $totalPoin = $soalList->sum('poin');
        $totalDiperoleh = 0;
        $adaBelumDinilai = false;

        foreach ($soalList as $soal) {
            $jawaban = $jawabanList->get($soal->id);

            if (! $jawaban || $jawaban->poin_diperoleh === null) {
                $adaBelumDinilai = true;

                continue;
            }
            $totalDiperoleh += $jawaban->poin_diperoleh;
        }

        if ($adaBelumDinilai) {
            $hasil->update(['status' => 'menunggu_penilaian', 'nilai' => null]);

            return;
        }

        $nilai = $totalPoin > 0 ? round(($totalDiperoleh / $totalPoin) * 100, 2) : 0;
        $hasil->update(['status' => 'selesai', 'nilai' => $nilai]);
    }
}
