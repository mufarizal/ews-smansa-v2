<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    protected $fillable = [
        'nama',
        'kode',
    ];

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function guru()
    {
        return $this->belongsToMany(Guru::class, 'guru_mapel_kelas')->withPivot('kelas_id')->withTimesStamps();
    }
}
