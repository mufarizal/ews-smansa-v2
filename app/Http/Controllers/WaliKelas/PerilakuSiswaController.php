<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Concerns\PerilakuSiswaActions;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class PerilakuSiswaController extends Controller
{
    use PerilakuSiswaActions;

    protected function kelasYangDiizinkan(): Collection
    {
        return Auth::user()->guru->kelasSebagaiWali;
    }

    protected function viewPrefix(): string
    {
        return 'wali_kelas.perilaku';
    }

    protected function routePrefix(): string
    {
        return 'wali_kelas.perilaku';
    }
}
