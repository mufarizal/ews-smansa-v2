<?php

namespace App\Http\Controllers\Kurikulum;

use App\Http\Controllers\Controller;
use App\Models\Mapel;
use Illuminate\Http\Request;

class MapelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mapels = Mapel::orderBy('nama')->paginate(10);

        return view('kurikulum.mapels.index', compact('mapels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('kurikulum.mapels.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'kode' => 'nullable|string|max:20|unique:mapels,kode',
        ]);

        Mapel::create($validated);

        return redirect()->route('kurikulum.mapels.index')->with('success', 'Mata Pelajaran Berhasil Ditambahkan');
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
    public function edit(Mapel $mapel)
    {
        return view('kurikulum.mapels.edit', compact('mapel'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mapel $mapel)
    {
        $validated = $request->validate([
            'nama' => 'sometimes|string|max:100',
            'kode' => 'nullable|string|max:20|unique:mapels,kode',
        ]);

        $mapel->update($validated);

        return redirect()->route('kurikulum.mapels.index')->with('success', 'Mata Pelajaran Berhasil Diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mapel $mapel)
    {
        $mapel->delete();

        return back()->with('success', 'Mata Pelajaran Berhasil Dihapus');
    }
}
