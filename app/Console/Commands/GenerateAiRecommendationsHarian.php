<?php

namespace App\Console\Commands;

use App\Services\AIService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class GenerateAiRecommendationsHarian extends Command
{
    protected $signature = 'ai:generate-harian
                            {--kelas= : Generate hanya untuk 1 kelas_id tertentu}
                            {--siswa= : Generate hanya untuk 1 siswa_id tertentu (versi self-reflection)}
                            {--tanggal= : Tanggal EWS yang jadi dasar rekomendasi (Y-m-d), default hari ini}';

    protected $description = 'Generate AI recommendation berdasarkan hasil Early Warning System hari ini';

    public function handle(AIService $aiService): int
    {
        $tanggal = $this->option('tanggal') ?? Carbon::today()->toDateString();

        if ($siswaId = $this->option('siswa')) {
            $this->info("Generate rekomendasi self-reflection untuk siswa {$siswaId}...");
            $aiService->generateUntukSiswa((int) $siswaId, $tanggal);
        } elseif ($kelasId = $this->option('kelas')) {
            $this->info("Generate rekomendasi untuk kelas {$kelasId}...");
            $aiService->generateUntukKelas((int) $kelasId, $tanggal);
        } else {
            $this->info("Generate rekomendasi untuk semua kelas & siswa, tanggal {$tanggal}...");
            $aiService->generateHarian($tanggal);
        }

        $this->info('Selesai.');

        return self::SUCCESS;
    }
}
