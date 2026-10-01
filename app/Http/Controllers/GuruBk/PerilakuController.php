<?php

namespace App\Http\Controllers\GuruBk;

use App\Http\Controllers\Controller;
use App\Models\Perilaku;
use Illuminate\Http\Request;

class PerilakuController extends Controller
{
    public function index()
    {
        $perilakus = Perilaku::orderBy('jenis')->orderBy('nama')->paginate(15);

        return view('guru_bk.perilaku.index', compact('perilakus'));
    }

    public function create()
    {
        return view('guru_bk.perilaku.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'jenis' => ['required', 'in:positif,negatif'],
            'poin' => ['required', 'integer', 'min:1'],
            'keterangan' => ['nullable', 'string'],
        ]);

        Perilaku::create($validated);

        return redirect()->route('guru_bk.perilaku.index')->with('success', 'Jenis perilaku berhasil ditambahkan.');
    }

    public function edit(Perilaku $perilaku)
    {
        return view('guru_bk.perilaku.edit', compact('perilaku'));
    }

    public function update(Request $request, Perilaku $perilaku)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'jenis' => ['required', 'in:positif,negatif'],
            'poin' => ['required', 'integer', 'min:1'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $perilaku->update($validated);

        return redirect()->route('guru_bk.perilaku.index')->with('success', 'Jenis perilaku berhasil diperbarui.');
    }

    public function destroy(Perilaku $perilaku)
    {
        if ($perilaku->perilakuSiswas()->exists()) {
            return back()->with('error', 'Tidak bisa dihapus karena sudah pernah dipakai mencatat perilaku siswa.');
        }

        $perilaku->delete();

        return back()->with('success', 'Jenis perilaku berhasil dihapus.');
    }

    /**
     * Jadikan perilaku positif ini sebagai default untuk fitur "Tandai Semua Aman".
     * Hanya 1 yang boleh aktif — yang lain otomatis dinonaktifkan.
     */
    public function jadikanDefaultAman(Perilaku $perilaku)
    {
        if ($perilaku->jenis !== 'positif') {
            return back()->with('error', 'Hanya perilaku jenis positif yang bisa dijadikan default "Kelas Aman".');
        }

        Perilaku::where('id', '!=', $perilaku->id)->update(['is_default_aman' => false]);
        $perilaku->update(['is_default_aman' => true]);

        return back()->with('success', "\"{$perilaku->nama}\" sekarang jadi default untuk Kelas Aman.");
    }
}
