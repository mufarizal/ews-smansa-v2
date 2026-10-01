<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuruBkKelas extends Model
{
    protected $table = 'guru_bk_kelas';

    protected $fillable = [
        'guru_id',
        'kelas_id',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}
