<?php

namespace App\Http\Controllers\Kurikulum;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\GuruBkKelas;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GuruBkKelasController extends Controller
{
    public function index()
    {
        $penugasans = GuruBkKelas::with(['guru',  'kelas'])->orderBy('kelas_id')->paginate(15);

        return view('kurikulum.guru_bk_kelas.index', compact('penugasans'));
    }

    public function create()
    {
        $gurus = Guru::orderBy('nama')->get();
        $kelasList = Kelas::whereDoesntHave('guruBk')->orderBy('tingkat')->orderBy('nama')->get();

        return view('kurikulum.guru_bk_kelas.create', compact('gurus', 'kelasList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'guru_id' => 'required|exists:gurus,id',
            'kelas_id' => ['required', 'array', 'min:1'],
            'kelas_id.*' => ['integer', 'distinct', 'exists:kelas,id'],
        ]);

        DB::transaction(function () use ($validated): void {
            $kelasIds = $validated['kelas_id'];
            $kelasYangSudahDitugaskan = GuruBkKelas::query()
                ->whereIn('kelas_id', $kelasIds)
                ->pluck('kelas_id')
                ->all();

            if ($kelasYangSudahDitugaskan !== []) {
                throw ValidationException::withMessages([
                    'kelas_id' => 'Satu atau beberapa kelas sudah memiliki guru BK.',
                ]);
            }

            foreach ($kelasIds as $kelasId) {
                GuruBkKelas::create([
                    'guru_id' => $validated['guru_id'],
                    'kelas_id' => $kelasId,
                ]);
            }
        });

        return redirect()
            ->route('kurikulum.guru-bk-kelas.index')
            ->with('success', 'Penugasan Guru BK berhasil ditambahkan untuk '.count($validated['kelas_id']).' kelas.');
    }

    public function destroy(GuruBkKelas $guruBkKelas)
    {
        $guruBkKelas->delete();

        return back()->with('success', 'Penugasan Guru BK Berhasil Dihapus');
    }
}
