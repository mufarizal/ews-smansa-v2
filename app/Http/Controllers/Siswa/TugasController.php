<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\JawabanTugas;
use App\Models\SoalTugas;
use App\Models\Tugas;
use App\Services\TugasService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TugasController extends Controller
{
    public function __construct(private TugasService $tugasService) {}

    public function index()
    {
        $siswa = Auth::user()->siswa;
        abort_if(! $siswa, 403);

        $tugasList = Tugas::with('mapel')->where('kelas_id', $siswa->kelas_id)
            ->where('is_published', true)->orderByDesc('tanggal_selesai')->get()
            ->map(function (Tugas $tugas) use ($siswa) {
                $tugas->nilai_tugas = $this->tugasService->pastikanNilaiTugas($tugas, $siswa);

                return $tugas;
            });

        return view('siswa.tugas.index', compact('tugasList'));
    }

    public function show(Tugas $tugas)
    {
        $siswa = Auth::user()->siswa;
        $this->authorizeSiswa($tugas, $siswa);

        $soalList = $tugas->soalTugas()->orderBy('urutan')->get();
        $jawabanList = JawabanTugas::whereIn('soal_tugas_id', $soalList->pluck('id'))
            ->where('siswa_id', $siswa->id)->get()->keyBy('soal_tugas_id');
        $nilaiTugas = $this->tugasService->pastikanNilaiTugas($tugas, $siswa);

        return view('siswa.tugas.show', compact('tugas', 'soalList', 'jawabanList', 'nilaiTugas'));
    }

    public function jawabStore(Request $request, Tugas $tugas, SoalTugas $soal)
    {
        $siswa = Auth::user()->siswa;
        $this->authorizeSiswa($tugas, $siswa);
        abort_if($soal->tugas_id !== $tugas->id, 404);

        $nilaiTugas = $this->tugasService->pastikanNilaiTugas($tugas, $siswa);

        if (in_array($nilaiTugas->status, ['menunggu_penilaian', 'selesai'])) {
            return back()->with('error', 'Tugas sudah dikumpulkan, Jawaban tidak dapat diubah.');
        }

        if (now()->gt($tugas->tanggal_selesai)) {
            return back()->with('error', 'Waktu pengumpulan tugas sudah berakhir.');
        }

        $payload = [];

        if ($soal->tipe_soal === 'pilihan_ganda') {
            $request->validate(['jawaban_teks' => 'required|in:a,b,c,d']);
            $payload['jawaban_teks'] = $request->jawaban_teks;
        } elseif ($soal->tipe_soal === 'esai') {
            $request->validate(['jawaban_teks' => 'required|string']);
            $payload['jawaban_teks'] = $request->jawaban_teks;
        } else {
            $request->validate(['file' => 'required|file|mimes:jpg,pdf,jpeg,png,doc,docx|max:5120']);
            $payload['file'] = $request->file('file')->store('tugas-jawaban', 'public');
        }

        $this->tugasService->simpanJawaban($soal, $siswa, $payload);

        return back()->with('success', 'Jawaban berhasil disimpan.');
    }

    public function submitAkhir(Tugas $tugas)
    {
        $siswa = Auth::user()->siswa;
        $this->authorizeSiswa($tugas, $siswa);

        $this->tugasService->submitAkhir($tugas, $siswa);

        return redirect()->route('siswa.tugas.index')->with('success', 'Tugas berhasil dikumpulkan.');
    }

    private function authorizeSiswa(Tugas $tugas, $siswa)
    {
        abort_if(! $siswa || $tugas->kelas_id !== $siswa->kelas_id || ! $tugas->is_published, 403);
    }
}
