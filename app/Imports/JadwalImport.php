<?php

namespace App\Imports;

use App\Models\GuruMapelKelas;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Semester;
use App\Services\JadwalBentrokChecker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class JadwalImport implements ToCollection, WithHeadingRow
{
    public int $berhasil = 0;

    /** @var array<int, array{baris: int, pesan: string}> */
    public array $gagal = [];

    private ?Semester $semesterAktif;

    /** @var Collection<string, int> nama kelas (lowercase) => id */
    private Collection $petaKelas;

    public function __construct()
    {
        $this->semesterAktif = Semester::aktif();
        $this->petaKelas = Kelas::pluck('id', 'nama')
            ->mapWithKeys(fn ($id, $nama) => [strtolower(trim($nama)) => $id]);
    }

    public function collection(Collection $rows): void
    {
        if (! $this->semesterAktif) {
            $this->gagal[] = [
                'baris' => '-',
                'pesan' => 'Tidak ada semester aktif. Aktifkan semester dulu sebelum import jadwal.',
            ];

            return;
        }

        foreach ($rows as $index => $row) {
            $nomorBaris = $index + 2;

            $namaKelas = $this->bersihkan($row['kelas'] ?? null);
            $namaMapel = $this->bersihkan($row['mapel'] ?? null);
            $nipGuru = $this->bersihkan($row['guru_nip'] ?? null);
            $hari = strtolower($this->bersihkan($row['hari'] ?? null) ?? '');
            $jamMulai = $this->bersihkanJam($row['jam_mulai'] ?? null);
            $jamSelesai = $this->bersihkanJam($row['jam_selesai'] ?? null);

            if (empty($namaKelas) && empty($namaMapel) && empty($nipGuru)) {
                continue; // baris kosong, lewati
            }

            $validator = Validator::make(
                compact('namaKelas', 'namaMapel', 'nipGuru', 'hari', 'jamMulai', 'jamSelesai'),
                [
                    'namaKelas' => ['required', 'string'],
                    'namaMapel' => ['required', 'string'],
                    'nipGuru' => ['required', 'string'],
                    'hari' => ['required', 'in:senin,selasa,rabu,kamis,jumat,sabtu'],
                    'jamMulai' => ['required', 'date_format:H:i'],
                    'jamSelesai' => ['required', 'date_format:H:i', 'after:jamMulai'],
                ]
            );

            if ($validator->fails()) {
                $this->gagal[] = [
                    'baris' => $nomorBaris,
                    'pesan' => implode(' ', $validator->errors()->all()),
                ];

                continue;
            }

            // cari penugasan (guru_mapel_kelas) berdasarkan kombinasi kelas+mapel+nip guru
            $penugasan = GuruMapelKelas::query()
                ->whereHas('kelas', fn ($q) => $q->whereRaw('LOWER(nama) = ?', [strtolower($namaKelas)]))
                ->whereHas('mapel', fn ($q) => $q->whereRaw('LOWER(nama) = ?', [strtolower($namaMapel)]))
                ->whereHas('guru', fn ($q) => $q->where('nip', $nipGuru))
                ->with(['guru', 'mapel', 'kelas'])
                ->first();

            if (! $penugasan) {
                $this->gagal[] = [
                    'baris' => $nomorBaris,
                    'pesan' => "Kombinasi kelas \"{$namaKelas}\", mapel \"{$namaMapel}\", guru NIP {$nipGuru} belum ada di Penugasan Mengajar.",
                ];

                continue;
            }

            $bentrok = JadwalBentrokChecker::cek(
                semesterId: $this->semesterAktif->id,
                hari: $hari,
                jamMulai: $jamMulai,
                jamSelesai: $jamSelesai,
                guruId: $penugasan->guru_id,
                kelasId: $penugasan->kelas_id,
            );

            if ($bentrok) {
                $this->gagal[] = [
                    'baris' => $nomorBaris,
                    'pesan' => $bentrok,
                ];

                continue;
            }

            Jadwal::create([
                'semester_id' => $this->semesterAktif->id,
                'kelas_id' => $penugasan->kelas_id,
                'guru_id' => $penugasan->guru_id,
                'mapel_id' => $penugasan->mapel_id,
                'hari' => $hari,
                'jam_mulai' => $jamMulai,
                'jam_selesai' => $jamSelesai,
            ]);

            $this->berhasil++;
        }
    }

    private function bersihkan(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = (string) $value;
        $value = preg_replace('/[\x00-\x1F\x7F]/u', '', $value);
        $value = ltrim($value, "='\"");
        $value = trim($value);

        return $value === '' ? null : $value;
    }

    /**
     * Excel kadang nyimpen kolom jam sebagai objek DateTime, bukan string,
     * tergantung format sel di file aslinya. Handle keduanya.
     */
    private function bersihkanJam(mixed $value): ?string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('H:i');
        }

        return $this->bersihkan($value);
    }
}
