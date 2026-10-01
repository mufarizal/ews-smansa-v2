<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Override;

class KehadiranGuru extends Model
{
    protected $fillable = [
        'guru_id',
        'tanggal',
        'sesi',
        'status',
        'keterangan',
        'waktu_absen',
    ];

    #[Override]
    protected function casts()
    {
        return [
            'tanggal' => 'date',
            'waktu_absen' => 'datetime',
        ];
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }
}
