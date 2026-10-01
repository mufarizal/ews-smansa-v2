<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EarlyWarningResult extends Model
{
    protected $fillable = [
        'semester_id',
        'siswa_id',
        'kelas_id',
        'generated_by',
        'tanggal_hitung',
        'generated_at',
        'c1_akademik',
        'c2_absensi',
        'c3_perilaku',
        'total_perilaku_negatif',
        'total_perilaku_positif',
        'r1_absensi',
        'r2_perilaku',
        'r3_akademik',
        'skor_akhir',
        'kategori',
        'data_tidak_lengkap',
        'input_metadata',
    ];

    protected function casts()
    {
        return [
            'tanggal_hitung' => 'date',
            'generated_at' => 'datetime',
            'data_tidak_lengkap' => 'boolean',
            'input_metadata' => 'array',
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

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
