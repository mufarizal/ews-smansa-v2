<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AkunSiswaController extends Controller
{
    public function index(Request $request)
    {
        $siswas = Siswa::with(['user', 'kelas'])
            ->when($request->filled('cari'), function ($q) use ($request) {
                $q->where(function ($q2) use ($request) {
                    $q2->where('nama', 'like', '%'.$request->cari.'%')
                        ->orWhere('nis', 'like', '%'.$request->cari.'%');
                });
            })
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        return view('admin.akun_siswa.index', compact('siswas'));
    }

    public function toggleAktif(Siswa $siswa)
    {
        $siswa->user->update(['is_active' => ! $siswa->user->is_active]);

        $status = $siswa->user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Akun {$siswa->nama} berhasil {$status}.");
    }

    public function resetPassword(Siswa $siswa)
    {
        $siswa->user->update(['password' => Hash::make(config('school.default_password'))]);

        return back()->with('success', "Password {$siswa->nama} berhasil direset ke default.");
    }
}
