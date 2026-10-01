<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Perilaku;
use App\Models\PerilakuSiswa;
use App\Models\Siswa;

class PerilakuSiswaService
{
    public function simpanIndividual(Siswa $siswa, Perilaku $perilaku, Guru $guruInput, string $tanggal, ?string $catatan): PerilakuSiswa
    {
        return PerilakuSiswa::create([
            'siswa_id' => $siswa->id,
            'perilaku_id' => $perilaku->id,
            'guru_id' => $guruInput->id,
            'tanggal' => $tanggal,
            'catatan' => $catatan,
        ]);
    }

    /**
     * Tandai semua siswa "aman" (tidak izin/sakit/alpha di tanggal ini) dengan perilaku
     * positif default. Return jumlah siswa yang ditandai.
     */
    public function bulkKelasAman(Kelas $kelas, Guru $guruInput, string $tanggal): int
    {
        $perilakuDefault = Perilaku::defaultAman();

        if (! $perilakuDefault) {
            throw new \RuntimeException('Belum ada perilaku positif yang dijadikan default. Hubungi Guru BK untuk mengaturnya.');
        }

        $siswaBermasalah = Absensi::whereIn('siswa_id', $kelas->siswas()->pluck('id'))
            ->where('tanggal', $tanggal)
            ->whereIn('status', ['izin', 'sakit', 'alpha'])
            ->pluck('siswa_id')
            ->unique();

        $siswaAman = $kelas->siswas()->whereNotIn('id', $siswaBermasalah)->get();

        foreach ($siswaAman as $siswa) {
            PerilakuSiswa::create([
                'siswa_id' => $siswa->id,
                'perilaku_id' => $perilakuDefault->id,
                'guru_id' => $guruInput->id,
                'tanggal' => $tanggal,
                'catatan' => 'Ditandai otomatis via "Tandai Semua Aman".',
            ]);
        }

        return $siswaAman->count();
    }

    /**
     * Terapkan 1 jenis perilaku negatif ke siswa-siswa yang dicentang guru.
     *
     * @param  array<int>  $siswaIds
     */
    public function bulkKelasBermasalah(Perilaku $perilaku, array $siswaIds, Guru $guruInput, string $tanggal): int
    {
        $count = 0;

        foreach ($siswaIds as $siswaId) {
            PerilakuSiswa::create([
                'siswa_id' => $siswaId,
                'perilaku_id' => $perilaku->id,
                'guru_id' => $guruInput->id,
                'tanggal' => $tanggal,
            ]);
            $count++;
        }

        return $count;
    }
}
