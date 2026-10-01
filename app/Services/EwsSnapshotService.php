<?php

namespace App\Services;

use App\Models\AiRecommendation;
use App\Models\EarlyWarningResult;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Support\Collection;

class EwsSnapshotService
{
    public function latestResult(Siswa $siswa): ?EarlyWarningResult
    {
        return EarlyWarningResult::where('siswa_id', $siswa->id)
            ->orderByDesc('tanggal_hitung')
            ->first();
    }

    /**
     * Tren skor beberapa hari terakhir, urut dari yang terlama ke terbaru (buat grafik nanti).
     */
    public function trend(Siswa $siswa, int $hariTerakhir = 14): Collection
    {
        return EarlyWarningResult::where('siswa_id', $siswa->id)
            ->where('tanggal_hitung', '>=', now()->subDays($hariTerakhir)->toDateString())
            ->orderBy('tanggal_hitung')
            ->get(['tanggal_hitung', 'skor_akhir', 'kategori']);
    }

    /**
     * Ringkasan jumlah siswa per kategori di 1 kelas, berdasarkan hasil EWS TERBARU
     * masing-masing siswa (bukan harus tanggal yang sama persis).
     *
     * @return array{aman: int, perhatian: int, binaan: int, belum_ada_data: int}
     */
    public function ringkasanKelas(Kelas $kelas): array
    {
        $ringkasan = ['aman' => 0, 'perhatian' => 0, 'binaan' => 0, 'belum_ada_data' => 0];

        foreach ($kelas->siswas as $siswa) {
            $hasil = $this->latestResult($siswa);
            $ringkasan[$hasil?->kategori ?? 'belum_ada_data']++;
        }

        return $ringkasan;
    }

    /**
     * Daftar siswa 1 kelas, diurutkan prioritas: binaan dulu, lalu perhatian, lalu aman,
     * lalu yang belum ada data di paling bawah. Tiap siswa dilampiri ->ews_terkini.
     */
    public function siswaUrutPrioritas(Kelas $kelas): Collection
    {
        $urutanKategori = ['binaan' => 0, 'perhatian' => 1, 'aman' => 2];

        return $kelas->siswas()->orderBy('nama')->get()
            ->map(function (Siswa $siswa) {
                $siswa->ews_terkini = $this->latestResult($siswa);

                return $siswa;
            })
            ->sortBy(fn (Siswa $siswa) => [
                $urutanKategori[$siswa->ews_terkini?->kategori] ?? 99,
                $siswa->ews_terkini?->skor_akhir ?? 0,
            ])
            ->values();
    }

    /**
     * Ambil rekomendasi AI terbaru untuk siswa, sesuai tipe ('guru' atau 'siswa').
     */
    public function rekomendasi(Siswa $siswa, string $tipe): ?AiRecommendation
    {
        return AiRecommendation::where('siswa_id', $siswa->id)
            ->where('tipe', $tipe)
            ->orderByDesc('tanggal_hitung')
            ->first();
    }
}
