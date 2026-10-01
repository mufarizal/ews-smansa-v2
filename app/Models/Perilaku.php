<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perilaku extends Model
{
    protected $fillable = [
        'nama',
        'jenis',
        'poin',
        'keterangan',
        'is_default_aman',
    ];

    protected function casts(): array
    {
        return [
            'is_default_aman' => 'boolean',
        ];
    }

    public function perilakuSiswas(): HasMany
    {
        return $this->hasMany(PerilakuSiswa::class);
    }

    public static function defaultAman(): ?self
    {
        return static::where('is_default_aman', true)->first();
    }
}
