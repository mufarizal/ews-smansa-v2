<?php

namespace App\Services;

use App\Models\Jadwal;

class JadwalBentrokChecker
{
    /**
     * Cek bentrok: guru yang sama gak boleh ngajar 2 tempat di jam bersamaan,
     * dan 1 kelas gak boleh punya 2 jadwal bertabrakan di hari & jam yang sama.
     * Return null kalau aman, atau string alasan kalau bentrok.
     */
    public static function cek(
        int $semesterId,
        string $hari,
        string $jamMulai,
        string $jamSelesai,
        int $guruId,
        int $kelasId,
        ?int $excludeJadwalId = null,
    ): ?string {
        $overlap = fn ($q) => $q->where('semester_id', $semesterId)
            ->where('hari', $hari)
            ->where('jam_mulai', '<', $jamSelesai)
            ->where('jam_selesai', '>', $jamMulai)
            ->when($excludeJadwalId, fn ($q2) => $q2->where('id', '!=', $excludeJadwalId));

        $bentrokGuru = Jadwal::where('guru_id', $guruId)->tap($overlap)->exists();
        if ($bentrokGuru) {
            return 'Guru sudah punya jadwal lain yang bertabrakan pada jam tersebut.';
        }

        $bentrokKelas = Jadwal::where('kelas_id', $kelasId)->tap($overlap)->exists();
        if ($bentrokKelas) {
            return 'Kelas sudah punya jadwal lain yang bertabrakan pada jam tersebut.';
        }

        return null;
    }
}
