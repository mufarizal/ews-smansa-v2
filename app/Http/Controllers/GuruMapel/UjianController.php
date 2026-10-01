<?php

namespace App\Http\Controllers\GuruMapel;

use App\Http\Controllers\Controller;
use App\Models\GuruMapelKelas;
use App\Models\HasilUjian;
use App\Models\JawabanUjian;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\SoalUjian;
use App\Models\UjianHarian;
use App\Services\UjianService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UjianController extends Controller
{
    //
    public function __construct(private UjianService $ujianService) {}

    public function index()
    {
        $guru = Auth::user()->guru;
        $ujianList = UjianHarian::with(['mapel', 'kelas'])
            ->where('guru_id', $guru->id)->orderByDesc('tanggal')->paginate(10);

        return view('guru_mapel.ujian.index', compact('ujianList'));
    }

    public function create()
    {
        $guru = Auth::user()->guru;
        $penugasans = GuruMapelKelas::with(['mapel', 'kelas'])->where('guru_id', $guru->id)->get();

        return view('guru_mapel.ujian.create', compact('penugasans'));
    }

    public function store(Request $request)
    {
        $guru = Auth::user()->guru;
        $validate = $request->validate([
            'guru_mapel_kelas_id' => 'required|exists:guru_mapel_kelas,id',
            'judul' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'tanggal' => 'required|date',
            'durasi_menit' => 'required|integer|min:5|max:300',
        ]);

        $penugasan = GuruMapelKelas::where('id', $validate['guru_mapel_kelas_id'])->where('guru_id', $guru->id)
            ->firstOrFail();
        $semesterAktif = Semester::aktif();
        abort_if(! $semesterAktif, 422, 'Tidak ada Semester Aktif');

        $ujian = UjianHarian::create([
            'guru_id' => $guru->id,
            'mapel_id' => $penugasan->mapel_id,
            'kelas_id' => $penugasan->kelas_id,
            'semester_id' => $semesterAktif->id,
            'judul' => $validate['judul'],
            'deskripsi' => $validate['deskripsi']
                ?? null,
            'tanggal' => $validate['tanggal'],
            'durasi_menit' => $validate['durasi_menit'],
        ]);

        return redirect()->route('guru_mapel.ujian.show', $ujian)->with('success', 'Ujian berhasil dibuat, silahkan tambahkan soal ujian');
    }

    public function show(UjianHarian $ujian)
    {
        $this->authorizeGuru($ujian);
        $ujian->load(['soalUjians' => fn ($q) => $q->orderBy('urutan')]);

        return view('guru_mapel.ujian.show', compact('ujian'));
    }

    public function edit(UjianHarian $ujian)
    {
        $this->authorizeGuru($ujian);

        return view('guru_mapel.ujian.edit', compact('ujian'));
    }

    public function update(Request $request, UjianHarian $ujian)
    {
        $this->authorizeGuru($ujian);
        $validate = $request->validate([
            'judul' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'tanggal' => 'required|date',
            'durasi_menit' => 'required|integer|min:5|max:300',
        ]);

        $ujian->update($validate);

        return redirect()->route('guru_mapel.ujian.show', $ujian)->with('success', 'Ujian berhasil diperbarui');
    }

    public function destroy(UjianHarian $ujian)
    {
        $this->authorizeGuru($ujian);
        if ($ujian->hasilUjians()->exists()) {
            return back()->with('error', 'Ujian tidak bisa dihapus karena sudah ada siswa yang mengerjakan');
        }

        $ujian->delete();

        return redirect()->route('guru_mapel.ujian.index')->with('success', 'Ujian berhasil dihapus');
    }

    public function publish(UjianHarian $ujian)
    {
        $this->authorizeGuru($ujian);
        if ($ujian->soalUjians()->count() === 0) {
            return back()->with('error', 'Ujian tidak bisa dipublish karena belum ada soal ujian');
        }

        $ujian->update(['is_published' => ! $ujian->is_published]);

        return back()->with('success', $ujian->is_published ? 'Ujian berhasil dipublish' : 'Ujian berhasil disembunyikan');
    }

    public function soalStore(Request $request, UjianHarian $ujian)
    {
        $this->authorizeGuru($ujian);
        $validate = $this->validasiSoal($request);
        SoalUjian::create([
            'ujian_harian_id' => $ujian->id,
            'urutan' => (int) $ujian->soalUjians()->max('urutan') + 1,
            ...$validate,
        ]);

        return back()->with('success', 'Soal ujian berhasil ditambahkan');
    }

    public function soalUpdate(Request $request, UjianHarian $ujian, SoalUjian $soal)
    {
        $this->authorizeGuru($ujian);
        abort_if($soal->ujian_harian_id !== $ujian->id, 404);
        $soal->update($this->validasiSoal($request));

        return back()->with('success', 'Soal ujian berhasil diperbarui');
    }

    public function soalDestroy(UjianHarian $ujian, SoalUjian $soal)
    {
        $this->authorizeGuru($ujian);
        abort_if($soal->ujian_harian_id !== $ujian->id, 404);
        $soal->delete();

        return back()->with('success', 'Soal ujian berhasil dihapus');
    }

    public function hasilIndex(UjianHarian $ujian)
    {
        $this->authorizeGuru($ujian);

        $siswas = $ujian->kelas->siswas()->orderBy('nama')->get();
        $hasilList = HasilUjian::where('ujian_harian_id', $ujian->id)->get()->keyBy('siswa_id');

        return view('guru_mapel.ujian.hasil', compact('ujian', 'siswas', 'hasilList'));
    }

    public function nilaiForm(UjianHarian $ujian, Siswa $siswa)
    {
        $this->authorizeGuru($ujian);

        $soalList = $ujian->soalUjians()->orderBy('urutan')->get();
        $jawabanList = JawabanUjian::whereIn('soal_ujian_id', $soalList->pluck('id'))
            ->where('siswa_id', $siswa->id)
            ->get()
            ->keyBy('soal_ujian_id');
        $hasil = HasilUjian::where('ujian_harian_id', $ujian->id)->where('siswa_id', $siswa->id)->firstOrFail();

        return view('guru_mapel.ujian.nilai', compact('ujian', 'siswa', 'soalList', 'jawabanList', 'hasil'));
    }

    public function nilaiStore(Request $request, UjianHarian $ujian, Siswa $siswa)
    {
        $this->authorizeGuru($ujian);

        $validated = $request->validate([
            'poin' => ['required', 'array'],
            'poin.*' => ['nullable', 'integer', 'min:0'],
        ]);

        foreach ($validated['poin'] as $soalId => $poin) {
            $soal = SoalUjian::where('id', $soalId)->where('ujian_harian_id', $ujian->id)->first();

            if (! $soal || $soal->tipe_soal === 'pilihan_ganda') {
                continue;
            }

            $poinFinal = min((int) $poin, $soal->poin);

            JawabanUjian::updateOrCreate(
                ['soal_ujian_id' => $soal->id, 'siswa_id' => $siswa->id],
                ['poin_diperoleh' => $poinFinal]
            );
        }

        $hasil = HasilUjian::where('ujian_harian_id', $ujian->id)->where('siswa_id', $siswa->id)->firstOrFail();
        $this->ujianService->hitungUlangNilai($ujian, $siswa, $hasil);

        return redirect()
            ->route('guru_mapel.ujian.hasil', $ujian)
            ->with('success', 'Penilaian untuk '.$siswa->nama.' berhasil disimpan.');
    }

    private function authorizeGuru(UjianHarian $ujian): void
    {
        abort_if($ujian->guru_id !== Auth::user()->guru->id, 403);
    }

    private function validasiSoal(Request $request): array
    {
        $data = $request->validate([
            'tipe_soal' => ['required', 'in:pilihan_ganda,esai'],
            'pertanyaan' => ['required', 'string'],
            'poin' => ['required', 'integer', 'min:1'],
            'opsi_a' => ['required_if:tipe_soal,pilihan_ganda', 'nullable', 'string'],
            'opsi_b' => ['required_if:tipe_soal,pilihan_ganda', 'nullable', 'string'],
            'opsi_c' => ['required_if:tipe_soal,pilihan_ganda', 'nullable', 'string'],
            'opsi_d' => ['required_if:tipe_soal,pilihan_ganda', 'nullable', 'string'],
            'kunci_jawaban' => ['required_if:tipe_soal,pilihan_ganda', 'nullable', 'in:A,B,C,D'],
        ]);

        if ($data['tipe_soal'] !== 'pilihan_ganda') {
            $data['opsi_a'] = $data['opsi_b'] = $data['opsi_c'] = $data['opsi_d'] = null;
            $data['kunci_jawaban'] = null;
        }

        return $data;
    }
}
