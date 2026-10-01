<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerilakuSiswa extends Model
{
    protected $fillable = [
        'siswa_id',
        'perilaku_id',
        'guru_id',
        'tanggal',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function perilaku(): BelongsTo
    {
        return $this->belongsTo(Perilaku::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }
}
