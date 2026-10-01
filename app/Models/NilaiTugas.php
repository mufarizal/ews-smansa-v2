<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Override;

class NilaiTugas extends Model
{
    protected $fillable = [
        'tugas_id',
        'siswa_id',
        'status',
        'nilai',
        'waktu_submit',
    ];

    #[Override]
    protected function casts()
    {
        return [
            'waktu_submit' => 'datetime',
            'nilai' => 'decimal:2',
        ];
    }

    public function tugas()
    {
        return $this->belongsTo(Tugas::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
