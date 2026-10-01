<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Override;

class Tugas extends Model
{
    protected $fillable = [
        'guru_id',
        'mapel_id',
        'kelas_id',
        'semester_id',
        'judul',
        'deskripsi',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_published',
    ];

    #[Override]
    protected function casts()
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'is_published' => 'boolean',
        ];
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function soalTugas()
    {
        return $this->hasMany(SoalTugas::class);
    }

    public function nilaiTugas()
    {
        return $this->hasMany(NilaiTugas::class);
    }
}
