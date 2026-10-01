<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $fillable = [
        'nama',
        'jenis',
        'tahun_ajaran',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_aktif',
    ];

    protected function casts()
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'is_aktif' => 'boolean',
        ];
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }

    public static function aktif()
    {
        return static::where('is_aktif', true)->first();
    }
}
