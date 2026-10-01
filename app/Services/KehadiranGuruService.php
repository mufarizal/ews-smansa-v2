<?php

namespace App\Services;

use App\Models\Guru;
use App\Models\KehadiranGuru;
use Illuminate\Support\Carbon;

class KehadiranGuruService
{
    public function catat(Guru $guru, string $sesi)
    {
        return KehadiranGuru::firstOrCreate(
            [
                'guru_id' => $guru->id,
                'tanggal' => Carbon::today()->toDateString(),
                'sesi' => $sesi,
            ],
            [
                'status' => 'hadir',
                'waktu_absen' => now(),
            ]
        );
    }

    public function kehadiranHariIni(Guru $guru)
    {
        $records = KehadiranGuru::where('guru_id', $guru->id)->where('tanggal', Carbon::today()->toDateString())->get()->keyBy('sesi');

        return [
            'pagi' => $records->get('pagi'),
            'sore' => $records->get('sore'),
        ];
    }
}
