<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $fillable = [
        'nama',
        'tingkat',
        'wali_kelas_id',
    ];

    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'wali_kelas_id');
    }

    public function siswas()
    {
        return $this->hasMany(Siswa::class);
    }

    public function guruBk()
    {
        return $this->belongsToMany(Guru::class, 'guru_bk_kelas')->withTimestamps();
    }

    public function gurus()
    {
        return $this->belongsToMany(Guru::class, 'guru_mapel_kelas')->withPivot('mapel_id')->withTimestamps();
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }
}
