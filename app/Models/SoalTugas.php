<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoalTugas extends Model
{
    protected $fillable = [
        'tugas_id',
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

    public function tugas()
    {
        return $this->belongsTo(Tugas::class);
    }

    public function jawabanTugas()
    {
        return $this->hasMany(JawabanTugas::class);
    }
}
