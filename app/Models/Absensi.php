<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Override;

class Absensi extends Model
{
    protected $fillable = [
        'siswa_id',
        'jadwal_id',
        'tanggal',
        'tipe',
        'status',
        'menit_terlambat',
    ];

    #[Override]
    protected function casts()
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }
}
