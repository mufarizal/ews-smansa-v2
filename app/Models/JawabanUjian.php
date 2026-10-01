<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabanUjian extends Model
{
    protected $fillable = [
        'soal_ujian_id',
        'siswa_id',
        'jawaban_teks',
        'poin_diperoleh',
    ];

    public function soalUjian()
    {
        return $this->belongsTo(SoalUjian::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
