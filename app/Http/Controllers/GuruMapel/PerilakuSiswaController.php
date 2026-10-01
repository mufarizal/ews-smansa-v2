<?php

namespace App\Http\Controllers\GuruMapel;

use App\Http\Controllers\Concerns\PerilakuSiswaActions;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class PerilakuSiswaController extends Controller
{
    use PerilakuSiswaActions;

    protected function kelasYangDiizinkan(): Collection
    {
        return Auth::user()->guru->kelasDiampu()->get()->unique('id')->values();
    }

    protected function viewPrefix(): string
    {
        return 'guru_mapel.perilaku';
    }

    protected function routePrefix(): string
    {
        return 'guru_mapel.perilaku';
    }
}
