<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Override;

class Siswa extends Model
{
    protected $fillable = [
        'user_id',
        'kelas_id',
        'nis',
        'nama',
        'jenis_kelamin',
        'tanggal_lahir',
        'alamat',
        'nama_orang_tua',
        'no_hp_orang_tua',
    ];

    #[Override]
    protected function casts()
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }

    public function jawabanTugas()
    {
        return $this->hasMany(JawabanTugas::class);
    }

    public function nilaiTugas()
    {
        return $this->hasMany(NilaiTugas::class);
    }

    public function jawabanUjians()
    {
        return $this->hasMany(JawabanUjian::class);
    }

    public function hasilUjians()
    {
        return $this->hasMany(HasilUjian::class);
    }

    public function perilakuSiswas()
    {
        return $this->hasMany(PerilakuSiswa::class);
    }

    public function ews_terkini()
    {
        return $this->hasOne(EarlyWarningResult::class)->latestOfMany('tanggal_hitung');
    }
}
