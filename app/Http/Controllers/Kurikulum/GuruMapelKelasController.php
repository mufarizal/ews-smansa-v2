<?php

namespace App\Http\Controllers\Kurikulum;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\GuruMapelKelas;
use App\Models\Kelas;
use App\Models\Mapel;
use Illuminate\Http\Request;

class GuruMapelKelasController extends Controller
{
    public function index()
    {
        $penugasans = GuruMapelKelas::with(['guru', 'mapel', 'kelas'])->orderBy('kelas_id')->paginate(15);

        return view('kurikulum.guru_mapel_kelas.index', compact('penugasans'));
    }

    public function create()
    {
        $gurus = Guru::orderBy('nama')->get();
        $mapels = Mapel::orderBy('nama')->get();
        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama')->get();

        return view('kurikulum.guru_mapel_kelas.create', compact('gurus', 'mapels', 'kelasList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'guru_id' => 'required|exists:gurus,id',
            'mapel_id' => 'required|exists:mapels,id',
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        $sudahAda = GuruMapelKelas::where($validated)->exists();

        if ($sudahAda) {
            return back()->withInput()->with('error', 'Guru Ini sudah Ditugaskan untuk Mata Pelajaran di kelas ini');
        }

        GuruMapelKelas::create($validated);

        return redirect()->route('kurikulum.guru-mapel-kelas.index')->with('success', 'Penugasan Mengajar berhasil Ditambahkan');
    }

    public function destroy(GuruMapelKelas $guruMapelKelas)
    {
        $guruMapelKelas->delete();

        return back()->with('success', 'Penugasan Mengajar Berhasil Dihapus');
    }
}
