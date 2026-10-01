<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    protected $fillable = [
        'user_id',
        'nip',
        'nama',
        'jenis_kelamin',
        'no_hp',
        'alamat',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelasDiampu()
    {
        return $this->belongsToMany(Kelas::class, 'guru_mapel_kelas')->withPivot('mapel_id')->withTimestamps();
    }

    public function mapels()
    {
        return $this->belongsToMany(Mapel::class, 'guru_mapel_kelas')->withPivot('kelas_id')->withTimestamps();
    }

    public function kelasSebagaiWali()
    {
        return $this->hasMany(Kelas::class, 'wali_kelas_id');
    }

    public function kelasBk()
    {
        return $this->belongsToMany(Kelas::class, 'guru_bk_kelas')->withTimestamps();
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function kehadirans()
    {
        return $this->hasMany(KehadiranGuru::class);
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class);
    }

    public function ujianHarians()
    {
        return $this->hasMany(UjianHarian::class);
    }

    public function perilakuSiswaDicatat()
    {
        return $this->hasMany(PerilakuSiswa::class);
    }
}
