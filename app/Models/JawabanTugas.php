<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabanTugas extends Model
{
    protected $fillable = [
        'soal_tugas_id',
        'siswa_id',
        'jawaban_text',
        'file_path',
        'poin_diperoleh',
    ];

    public function soalTugas()
    {
        return $this->belongsTo(SoalTugas::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
