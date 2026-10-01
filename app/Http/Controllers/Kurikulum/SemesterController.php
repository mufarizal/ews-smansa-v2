<?php

namespace App\Http\Controllers\Kurikulum;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    public function index()
    {
        $semesters = Semester::orderByDesc('tanggal_mulai')->paginate(10);

        return view('kurikulum.semesters.index', compact('semesters'));
    }

    public function create()
    {
        return view('kurikulum.semesters.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => 'required|in:ganjil,genap',
            'tahun_ajaran' => 'required|string|max:20',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
        ]);
        $semester = Semester::create($validated);

        if ($request->boolean('is_aktif')) {
            $this->jadikanAktif($semester);
        }

        return redirect()->route('kurikulum.semesters.index')->with('success', 'Semester Berhasil Ditambahkan');
    }

    public function edit(Semester $semester)
    {
        return view('kurikulum.semesters.edit', compact('semester'));
    }

    public function update(Request $request, Semester $semester)
    {
        $validated = $request->validate([
            'nama' => 'sometimes|string|max:255',
            'jenis' => 'sometimes|in:ganjil,genap',
            'tahun_ajaran' => 'sometimes|string|max:20',
            'tanggal_mulai' => 'sometimes|date',
            'tanggal_selesai' => 'sometimes|date|after:tanggal_mulai',
        ]);

        $semester->update($validated);

        if ($request->boolean('is_aktif')) {
            $this->jadikanAktif($semester);
        } elseif ($semester->is_aktif) {
            $semester->update(['is_aktif' => false]);
        }

        return redirect()->route('kurikulum.semesters.index')->with('success', 'Semester Berhasil Diperbarui');
    }

    public function destroy(Semester $semester)
    {
        if ($semester->jadwals()->exists()) {
            return back()->with('error', 'Semester tidak dapat dihapus karena memiliki jadwal terkait.');
        }

        if ($semester->is_aktif) {
            return back()->with('error', 'Semester aktif tidak dapat dihapus.');
        }

        $semester->delete();

        return back()->with('success', 'Semester Berhasil Dihapus');
    }

    public function aktifkan(Semester $semester)
    {
        Semester::where('is_aktif', true)->update(['is_aktif' => false]);
        $semester->update(['is_aktif' => true]);

        return back()->with('success', "Semester \"{$semester->nama}\" sekarang aktif.");
    }

    public function nonaktifkan(Semester $semester)
    {
        $semester->update(['is_aktif' => false]);

        return back()->with('success', "Semester \"{$semester->nama}\" dinonaktifkan.");
    }

    private function jadikanAktif(Semester $semester): void
    {
        Semester::where('id', '!=', $semester->id)->where('is_aktif', true)->update(['is_aktif' => false]);
        $semester->update(['is_aktif' => true]);
    }
}
