<?php

namespace App\Http\Controllers\Kurikulum;

use App\Http\Controllers\Controller;
use App\Models\GuruMapelKelas;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Semester;
use App\Services\JadwalBentrokChecker;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama')->get();
        $selectedKelasId = $request->integer('kelas_id') ?: null;

        $jadwals = Jadwal::with(['kelas', 'guru', 'mapel', 'semester'])
            ->when($selectedKelasId, fn ($q) => $q->where('kelas_id', $selectedKelasId))
            ->orderByRaw("
        CASE hari
            WHEN 'senin' THEN 1
            WHEN 'selasa' THEN 2
            WHEN 'rabu' THEN 3
            WHEN 'kamis' THEN 4
            WHEN 'jumat' THEN 5
            WHEN 'sabtu' THEN 6
            ELSE 7
        END ASC
    ")
            ->orderBy('jam_mulai')
            ->get();

        return view('kurikulum.jadwals.index', compact('jadwals', 'kelasList', 'selectedKelasId'));
    }

    public function create(Request $request)
    {
        $kelasId = $request->integer('kelas_id');

        if (! $kelasId) {
            return redirect()->route('kurikulum.jadwals.index')->with('error', 'Pilih kelas terlebih dahulu.');
        }

        $kelas = Kelas::findOrFail($kelasId);
        $semesterAktif = Semester::aktif();
        $penugasans = GuruMapelKelas::with(['guru', 'mapel'])->where('kelas_id', $kelasId)->get();

        return view('kurikulum.jadwals.create', compact('kelas', 'semesterAktif', 'penugasans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'guru_mapel_kelas_id' => 'required|exists:guru_mapel_kelas,id',
            'hari' => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        $semesterAktif = Semester::aktif();

        if (! $semesterAktif) {
            return back()->withInput()->with('error', 'Tidak ada semester Aktif. Silakan aktifkan semester terlebih dahulu.');
        }

        $penugasan = GuruMapelKelas::findOrFail($validated['guru_mapel_kelas_id']);

        $bentrok = JadwalBentrokChecker::cek(
            semesterId: $semesterAktif->id,
            hari: $validated['hari'],
            jamMulai: $validated['jam_mulai'],
            jamSelesai: $validated['jam_selesai'],
            guruId: $penugasan->guru_id,
            kelasId: $validated['kelas_id'],
        );

        if ($bentrok) {
            return back()->withInput()->with('error', $bentrok);
        }

        Jadwal::create([
            'semester_id' => $semesterAktif->id,
            'kelas_id' => $validated['kelas_id'],
            'guru_id' => $penugasan->guru_id,
            'mapel_id' => $penugasan->mapel_id,
            'hari' => $validated['hari'],
            'jam_mulai' => $validated['jam_mulai'],
            'jam_selesai' => $validated['jam_selesai'],
        ]);

        return redirect()->route('kurikulum.jadwals.index', ['kelas_id' => $validated['kelas_id']])->with('success', 'Jadwal Berhasil Ditambahkan.');
    }

    public function edit(Jadwal $jadwal)
    {
        $penugasans = GuruMapelKelas::with(['guru', 'mapel'])->where('kelas_id', $jadwal->kelas_id)->get();
        $penugasanSaatIni = $penugasans->first(fn ($p) => $p->guru_id === $jadwal->guru_id && $p->mapel_id === $jadwal->mapel_id);

        return view('kurikulum.jadwals.edit', compact('jadwal', 'penugasans', 'penugasanSaatIni'));
    }

    public function update(Request $request, Jadwal $jadwal)
    {
        $validated = $request->validate([
            'guru_mapel_kelas_id' => ['required', 'exists:guru_mapel_kelas,id'],
            'hari' => ['required', 'in:senin,selasa,rabu,kamis,jumat,sabtu'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
        ]);

        $penugasan = GuruMapelKelas::findOrFail($validated['guru_mapel_kelas_id']);

        $bentrok = JadwalBentrokChecker::cek(
            semesterId: $jadwal->semester_id,
            hari: $validated['hari'],
            jamMulai: $validated['jam_mulai'],
            jamSelesai: $validated['jam_selesai'],
            guruId: $penugasan->guru_id,
            kelasId: $jadwal->kelas_id,
            excludeJadwalId: $jadwal->id,
        );

        if ($bentrok) {
            return back()->withInput()->with('error', $bentrok);
        }

        $jadwal->update([
            'guru_id' => $penugasan->guru_id,
            'mapel_id' => $penugasan->mapel_id,
            'hari' => $validated['hari'],
            'jam_mulai' => $validated['jam_mulai'],
            'jam_selesai' => $validated['jam_selesai'],
        ]);

        return redirect()
            ->route('kurikulum.jadwals.index', ['kelas_id' => $jadwal->kelas_id])
            ->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Jadwal $jadwal)
    {
        $kelasId = $jadwal->kelas_id;
        $jadwal->delete();

        return redirect()
            ->route('kurikulum.jadwals.index', ['kelas_id' => $kelasId])
            ->with('success', 'Jadwal berhasil dihapus.');
    }
}
