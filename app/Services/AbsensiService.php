<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Jadwal;
use Illuminate\Support\Carbon;

class AbsensiService
{
    public const BATAS_HARI_LAMPAU = 7;

    public function tanggalMinimal()
    {
        return Carbon::today()->subDays(self::BATAS_HARI_LAMPAU)->toDateString();
    }

    public function tanggalMaksimal()
    {
        return Carbon::today()->toDateString();
    }

    public function storeBulk(Jadwal $jadwal, string $tanggal, array $data)
    {
        foreach ($data as $siswaId => $item) {
            Absensi::updateOrCreate(
                [
                    'siswa_id' => $siswaId,
                    'jadwal_id' => $jadwal->id,
                    'tanggal' => $tanggal,
                ],
                [
                    'tipe' => 'mapel',
                    'status' => $item['status'],
                    'menit_terlambat' => $item['status'] === 'terlambat' ? ($item['menit_terlambat'] ?? 0) : null,
                ]
            );
        }
    }
}
