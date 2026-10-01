<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilUjian extends Model
{
    protected $fillable = [
        'ujian_harian_id',
        'siswa_id',
        'waktu_mulai',
        'waktu_selesai',
        'status',
        'nilai',
    ];

    protected function casts()
    {
        return [
            'waktu_mulai' => 'datetime',
            'waktu_selesai' => 'datetime',
            'nilai' => 'decimal:2',
        ];
    }

    public function ujianHarian()
    {
        return $this->belongsTo(UjianHarian::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function batasWaktu()
    {
        return $this->waktu_mulai->copy()->addMinutes($this->ujianHarian->durasi_menit);
    }

    public function sisaDetik()
    {
        return max(0, now()->diffInSeconds($this->batasWaktu(), false));
    }

    public function sudahHabisWaktu()
    {
        return now()->gt($this->batasWaktu());
    }
}
