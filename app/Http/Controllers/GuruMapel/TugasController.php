<?php

namespace App\Http\Controllers\GuruMapel;

use App\Http\Controllers\Controller;
use App\Models\GuruMapelKelas;
use App\Models\JawabanTugas;
use App\Models\NilaiTugas;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SoalTugas;
use App\Models\Tugas;
use App\Services\TugasService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TugasController extends Controller
{
    public function __construct(private TugasService $tugasService) {}

    public function index(Request $request)
    {
        $guru = Auth::user()->guru;
        $tugasList = Tugas::with(['mapel', 'kelas'])->where('guru_id', $guru->id)->orderByDesc('tanggal_selesai')
            ->paginate(10);

        return view('guru_mapel.tugas.index', compact('tugasList'));
    }

    public function create()
    {
        $guru = Auth::user()->guru;
        $penugasans = GuruMapelKelas::with(['mapel', 'kelas'])->where('guru_id', $guru->id)->get();

        return view('guru_mapel.tugas.create', compact('penugasans'));
    }

    public function store(Request $request)
    {
        $guru = Auth::user()->guru;
        $validated = $request->validate([
            'guru_mapel_kelas_id' => 'required|exists:guru_mapel_kelas,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $penugasan = GuruMapelKelas::where('id', $validated['guru_mapel_kelas_id'])->where('guru_id', $guru->id)->firstOrFail();
        $semesterAktif = Semester::aktif();
        abort_if(! $semesterAktif, 422, 'Tidak ada semester aktif.');
        $tugas = Tugas::create([
            'guru_id' => $guru->id,
            'mapel_id' => $penugasan->mapel_id,
            'kelas_id' => $penugasan->kelas_id,
            'semester_id' => $penugasan->semester_id,
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'is_published' => false,
        ]);

        return redirect()->route('guru_mapel.tugas.show', $tugas)->with('success', 'Tugas berhasil dibuat. Silakan tambahkan soal untuk tugas ini.');
    }

    public function show(Tugas $tugas)
    {
        $this->authorizeGuru($tugas);
        $tugas->load(['soalTugas' => fn ($q) => $q->orderBy('urutan')]);

        return view('guru_mapel.tugas.edit', compact('tugas'));
    }

    public function edit(Tugas $tugas)
    {
        $this->authorizeGuru($tugas);

        return view('guru_mapel.tugas.edit', compact('tugas'));
    }

    public function update(Request $request, Tugas $tugas)
    {
        $this->authorizeGuru($tugas);

        $validated = $request->validate([
            'judul' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $tugas->update($validated);

        return redirect()->route('guru_mapel.tugas.show', $tugas)->with('succes', 'Tugas Berhasil Diperbarui');
    }

    public function destroy(Tugas $tugas)
    {
        $this->authorizeGuru($tugas);
        $sudahDikerjakan = $tugas->nilaiTugas()->whereIn('status', ['sedang_dikerjakan', 'selesai', 'selesai'])->exists();
        if ($sudahDikerjakan) {
            return back()->with('error', 'Tugas tidak dapat dihapus karena sudah dikerjakan oleh siswa.');
        }

        $tugas->delete();

        return redirect()->route('guru_mapel.tugas.index')->with('success', 'Tugas berhasil dihapus.');
    }

    public function publish(Tugas $tugas)
    {
        $this->authorizeGuru($tugas);
        if ($tugas->soalTugas()->count() === 0) {
            return back()->with('error', 'Tugas tidak dapat dipublikasikan karena belum memiliki soal.');
        }

        $tugas->update(['is_published' => ! $tugas->is_published]);

        return back()->with('success', $tugas->is_published ? 'Tugas berhasil dipublikasikan.' : 'Tugas berhasil disembunyikan.');
    }

    public function soalStore(Request $request, Tugas $tugas)
    {
        $this->authorizeGuru($tugas);
        $validated = $this->validasiSoal($request);
        SoalTugas::create([
            'tugas_id' => $tugas->id,
            'urutan' => (int) $tugas->soalTugas()->max('urutan') + 1,
            ...$validated,
        ]);

        return back()->with('success', 'Soal Berhasil ditambahkan.');
    }

    public function soalUpdate(Request $request, Tugas $tugas, SoalTugas $soal)
    {
        $this->authorizeGuru($tugas);
        abort_if($soal->tugas_id !== $tugas->id, 404);

        $soal->update($this->validasiSoal($request));

        return back()->with('success', 'Soal Berhasil diperbarui');
    }

    public function soalDestroy(Tugas $tugas, SoalTugas $soal)
    {
        $this->authorizeGuru($tugas);
        abort_if($soal->tugas_id !== $tugas->id, 404);

        $soal->delete();

        return back()->with('success', 'Soal Berhasil dihapus');
    }

    public function siswaIndex(Tugas $tugas)
    {
        $this->authorizeGuru($tugas);
        abort_if(! $tugas->is_published, 404);
        $siswas = $tugas->kelas->siswas()->orderBy('nama')->get();
        $nilaiList = NilaiTugas::where('tugas_id', $tugas->id)->get()->keyBy('siswa_id');

        return view('guru_mapel.tugas.siswa', compact('tugas', 'siswas', 'nilaiList'));
    }

    public function nilaiForm(Tugas $tugas, Siswa $siswa)
    {
        $this->authorizeGuru($tugas);

        $soalList = $tugas->soalTugas()->orderBy('urutan')->get();
        $jawabanList = JawabanTugas::whereIn('soal_tugas_id', $soalList->pluck('id'))
            ->where('siswa_id', $siswa->id)->get()->keyBy('soal_tugas_id');

        return view('guru_mapel.tugas.nilai', compact('tugas', 'siswa', 'soalList', 'jawabanList'));
    }

    public function nilaiStore(Request $request, Tugas $tugas, Siswa $siswa)
    {
        $this->authorizeGuru($tugas);

        $validated = $request->validate([
            'poin' => 'required|array',
            'poin.*' => 'nullable|integer|min:0',
        ]);

        foreach ($validated['poin'] as $soalId => $poin) {
            $soal = SoalTugas::where('id', $soalId)->where('tugas_id', $tugas->id)->first();
            if (! $soal || $soal->tipe_soal === 'pilihan_ganda') {
                continue;
            }

            $poinFinal = min((int) $poin, $soal->poin);

            JawabanTugas::updateOrCreate(
                ['soal_tugas_id' => $soal->id, 'siswa_id' => $siswa->id],
                ['poin_diperoleh' => $poinFinal]
            );
        }

        $this->tugasService->hitungUlangNilai($tugas, $siswa);

        return redirect()->route('guru_mapel.tugas.siswa', $tugas)->with('success', 'Penilaian Untuk '.$siswa->nama.' Berhasil Disimpan');
    }

    private function authorizeGuru(Tugas $tugas)
    {
        abort_if($tugas->guru_id !== Auth::user()->guru->id, 403);
    }

    private function validasiSoal(Request $request)
    {
        $data = $request->validate([
            'tipe_soal' => 'required|in:pilihan_ganda,esai,upload_file',
            'pertanyaan' => 'required|string',
            'poin' => 'nullable|integer|min:0',
            'opsi_a' => 'required_if:tipe_soal,pilihan_ganda|nullable|string',
            'opsi_b' => 'required_if:tipe_soal,pilihan_ganda|nullable|string',
            'opsi_c' => 'required_if:tipe_soal,pilihan_ganda|nullable|string',
            'opsi_d' => 'required_if:tipe_soal,pilihan_ganda|nullable|string',
            'kunci_jawaban' => 'required_if:tipe_soal,pilihan_ganda|nullable|string|in:a,b,c,d',
        ]);

        if ($data['tipe_soal'] !== 'pilihan_ganda') {
            $data['opsi_a'] = $data['opsi_b'] = $data['opsi_c'] = $data['opsi_d'] = null;
            $data['kunci_jawaban'] = null;
        }
    }
}
