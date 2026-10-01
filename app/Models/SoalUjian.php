<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoalUjian extends Model
{
    protected $fillable = [
        'ujian_harian_id',
        'tipe_soal',
        'pertanyaan',
        'opsi_a',
        'opsi_b',
        'opsi_c',
        'opsi_d',
        'kunci_jawaban',
        'poin',
        'urutan',
    ];

    public function ujianHarian()
    {
        return $this->belongsTo(UjianHarian::class);
    }

    public function jawabanUjians()
    {
        return $this->hasMany(JawabanUjian::class);
    }
}
