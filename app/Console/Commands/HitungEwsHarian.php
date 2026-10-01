<?php

namespace App\Console\Commands;

use App\Models\Semester;
use App\Services\SAWService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class HitungEwsHarian extends Command
{
    protected $signature = 'ews:hitung-harian
                            {--kelas= : Hitung hanya untuk 1 kelas_id tertentu}
                            {--tanggal= : Hitung untuk tanggal tertentu (Y-m-d), default hari ini}
                            {--backfill : Hitung ulang beberapa hari ke belakang}
                            {--from= : Tanggal awal untuk backfill (Y-m-d)}
                            {--to= : Tanggal akhir untuk backfill (Y-m-d)}';

    protected $description = 'Hitung skor Early Warning System (SAW) harian untuk semua siswa';

    public function handle(SAWService $sawService): int
    {
        $semesterAktif = Semester::aktif();

        if (! $semesterAktif) {
            $this->error('Tidak ada semester aktif. Perhitungan dibatalkan.');

            return self::FAILURE;
        }

        $tanggalList = $this->tentukanTanggalList();

        foreach ($tanggalList as $tanggal) {
            $this->info("Menghitung EWS untuk tanggal {$tanggal}...");

            if ($kelasId = $this->option('kelas')) {
                $sawService->generateUntukKelas((int) $kelasId, $semesterAktif, $tanggal);
            } else {
                $sawService->generateHarian($tanggal);
            }
        }

        $this->info('Selesai.');

        return self::SUCCESS;
    }

    private function tentukanTanggalList(): array
    {
        if ($this->option('backfill')) {
            $from = Carbon::parse($this->option('from') ?? Carbon::today()->subDays(7)->toDateString());
            $to = Carbon::parse($this->option('to') ?? Carbon::today()->toDateString());

            $tanggalList = [];
            for ($date = $from->copy(); $date->lte($to); $date->addDay()) {
                $tanggalList[] = $date->toDateString();
            }

            return $tanggalList;
        }

        return [$this->option('tanggal') ?? Carbon::today()->toDateString()];
    }
}
