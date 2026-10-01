<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Override;

class UjianHarian extends Model
{
    protected $fillable = [
        'guru_id',
        'mapel_id',
        'kelas_id',
        'semester_id',
        'judul',
        'deskripsi',
        'tanggal',
        'durasi_menit',
        'is_published',
    ];

    #[Override]
    protected function casts()
    {
        return [
            'tanggal' => 'date',
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

    public function soalUjians()
    {
        return $this->hasMany(SoalUjian::class);
    }

    public function hasilUjians()
    {
        return $this->hasMany(HasilUjian::class);
    }
}
