<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Kelas;
use App\Models\Perilaku;
use App\Models\PerilakuSiswa;
use App\Models\Siswa;
use App\Services\PerilakuSiswaService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

trait PerilakuSiswaActions
{
    /**
     * Wajib diimplementasi controller pemakai: kelas mana saja yang boleh diakses guru ini.
     */
    abstract protected function kelasYangDiizinkan(): Collection;

    /**
     * Wajib diimplementasi: prefix nama view, misal 'guru_mapel.perilaku' atau 'wali_kelas.perilaku'.
     */
    abstract protected function viewPrefix(): string;

    /**
     * Wajib diimplementasi: nama route prefix, misal 'guru_mapel.perilaku' atau 'wali_kelas.perilaku'.
     */
    abstract protected function routePrefix(): string;

    public function index()
    {
        $guru = Auth::user()->guru;

        $riwayat = PerilakuSiswa::with(['siswa', 'perilaku'])
            ->where('guru_id', $guru->id)
            ->orderByDesc('tanggal')
            ->paginate(15);

        return view($this->viewPrefix().'.index', compact('riwayat'));
    }

    public function create()
    {
        $kelasList = $this->kelasYangDiizinkan();
        $perilakus = Perilaku::orderBy('jenis')->orderBy('nama')->get();

        return view($this->viewPrefix().'.create', compact('kelasList', 'perilakus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => ['required', 'exists:siswas,id'],
            'perilaku_id' => ['required', 'exists:perilakus,id'],
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'catatan' => ['nullable', 'string'],
        ]);

        $siswa = Siswa::findOrFail($validated['siswa_id']);
        abort_if(! $this->kelasYangDiizinkan()->pluck('id')->contains($siswa->kelas_id), 403);

        $perilaku = Perilaku::findOrFail($validated['perilaku_id']);
        $guru = Auth::user()->guru;

        app(PerilakuSiswaService::class)->simpanIndividual($siswa, $perilaku, $guru, $validated['tanggal'], $validated['catatan'] ?? null);

        return redirect()->route($this->routePrefix().'.index')->with('success', 'Perilaku siswa berhasil dicatat.');
    }

    public function edit(PerilakuSiswa $perilakuSiswa)
    {
        abort_if($perilakuSiswa->guru_id !== Auth::user()->guru->id, 403);

        $perilakus = Perilaku::orderBy('jenis')->orderBy('nama')->get();

        return view($this->viewPrefix().'.edit', compact('perilakuSiswa', 'perilakus'));
    }

    public function update(Request $request, PerilakuSiswa $perilakuSiswa)
    {
        abort_if($perilakuSiswa->guru_id !== Auth::user()->guru->id, 403);

        $validated = $request->validate([
            'perilaku_id' => ['required', 'exists:perilakus,id'],
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'catatan' => ['nullable', 'string'],
        ]);

        $perilakuSiswa->update($validated);

        return redirect()->route($this->routePrefix().'.index')->with('success', 'Catatan perilaku berhasil diperbarui.');
    }

    public function destroy(PerilakuSiswa $perilakuSiswa)
    {
        abort_if($perilakuSiswa->guru_id !== Auth::user()->guru->id, 403);

        $perilakuSiswa->delete();

        return back()->with('success', 'Catatan perilaku berhasil dihapus.');
    }

    public function bulkKelasAmanForm()
    {
        $kelasList = $this->kelasYangDiizinkan();

        return view($this->viewPrefix().'.bulk-aman', compact('kelasList'));
    }

    public function bulkKelasAmanStore(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
        ]);

        $kelas = Kelas::findOrFail($validated['kelas_id']);
        abort_if(! $this->kelasYangDiizinkan()->pluck('id')->contains($kelas->id), 403);

        $guru = Auth::user()->guru;

        try {
            $jumlah = app(PerilakuSiswaService::class)->bulkKelasAman($kelas, $guru, $validated['tanggal']);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route($this->routePrefix().'.index')->with('success', "{$jumlah} siswa berhasil ditandai aman.");
    }

    public function bulkKelasBermasalahForm(Request $request)
    {
        $kelasList = $this->kelasYangDiizinkan();
        $perilakuNegatif = Perilaku::where('jenis', 'negatif')->orderBy('nama')->get();

        $kelasTerpilih = null;
        $siswas = collect();

        if ($request->filled('kelas_id')) {
            $kelasTerpilih = Kelas::find($request->kelas_id);
            if ($kelasTerpilih && $this->kelasYangDiizinkan()->pluck('id')->contains($kelasTerpilih->id)) {
                $siswas = $kelasTerpilih->siswas()->orderBy('nama')->get();
            }
        }

        return view($this->viewPrefix().'.bulk-bermasalah', compact('kelasList', 'perilakuNegatif', 'kelasTerpilih', 'siswas'));
    }

    public function bulkKelasBermasalahStore(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'perilaku_id' => ['required', 'exists:perilakus,id'],
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'siswa_ids' => ['required', 'array', 'min:1'],
            'siswa_ids.*' => ['exists:siswas,id'],
        ]);

        $kelas = Kelas::findOrFail($validated['kelas_id']);
        abort_if(! $this->kelasYangDiizinkan()->pluck('id')->contains($kelas->id), 403);

        $perilaku = Perilaku::where('id', $validated['perilaku_id'])->where('jenis', 'negatif')->firstOrFail();
        $guru = Auth::user()->guru;

        $jumlah = app(PerilakuSiswaService::class)->bulkKelasBermasalah($perilaku, $validated['siswa_ids'], $guru, $validated['tanggal']);

        return redirect()->route($this->routePrefix().'.index')->with('success', "{$jumlah} siswa berhasil dicatat perilaku negatifnya.");
    }
}
