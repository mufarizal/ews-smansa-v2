<?php

namespace App\Http\Controllers\Kurikulum;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kelas = Kelas::with('waliKelas')->withCount('siswas')->orderBy('tingkat')->orderBy('nama')->paginate(10);

        return view('kurikulum.kelas.index', compact('kelas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $gurus = Guru::orderBy('nama')->get();

        return view('kurikulum.kelas.create', compact('gurus'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:10',
            'tingkat' => 'required|string|max:10',
            'wali_kelas_id' => 'nullable|exists:gurus,id',
        ]);

        Kelas::create($validated);

        return redirect()->route('kurikulum.kelas.index')->with('success', 'Kelas Berhasil Ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kelas $kelas)
    {
        $gurus = Guru::orderBy('nama')->get();

        return view('kurikulum.kelas.edit', compact('kelas', 'gurus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'nama' => 'sometimes|string|max:10',
            'tingkat' => 'sometimes|string|max:10',
            'wali_kelas_id' => 'nullable|exists:gurus,id',
        ]);

        $kelas->update($validated);

        return redirect()->route('kurikulum.kelas.index')->with('success', 'Kelas Berhasil Diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kelas $kelas)
    {
        if ($kelas->siswas()->exists()) {
            return back()->with('error', 'Kelas tidak dapat dihapus Karena memiliki Siswa terkait.');
        }

        if ($kelas->jadwals()->exists()) {
            return back()->with('error', 'Kelas tidak dapat dihapus Karena memiliki Jadwal terkait.');
        }
        $kelas->delete();

        return back()->with('success', 'Kelas Berhasil Dihapus');
    }
}
