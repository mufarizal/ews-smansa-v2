<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Override;

class AiRecommendation extends Model
{
    protected $fillable = [
        'siswa_id',
        'kelas_id',
        'semester_id',
        'tanggal_hitung',
        'tipe',
        'kategori',
        'penyebab',
        'saran',
        'provider_used',
    ];

    #[Override]
    protected function casts()
    {
        return [
            'tanggal_hitung' => 'date',
            'penyebab' => 'array',
            'saran' => 'array',
        ];
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(AiRecommendationFeedback::class);
    }
}
