<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\HasilUjian;
use App\Models\JawabanUjian;
use App\Models\SoalUjian;
use App\Models\UjianHarian;
use App\Services\UjianService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UjianController extends Controller
{
    public function __construct(private UjianService $ujianService) {}

    public function index()
    {
        $siswa = Auth::user()->siswa;
        abort_if(! $siswa, 403);

        $ujianList = UjianHarian::with('mapel')
            ->where('kelas_id', $siswa->kelas_id)
            ->where('is_published', true)
            ->orderByDesc('tanggal')
            ->get()
            ->map(function (UjianHarian $ujian) use ($siswa) {
                $ujian->hasil = HasilUjian::where('ujian_harian_id', $ujian->id)
                    ->where('siswa_id', $siswa->id)
                    ->first();

                return $ujian;
            });

        return view('siswa.ujian.index', compact('ujianList'));
    }

    /**
     * Halaman info ujian sebelum dimulai (kalau belum ada HasilUjian).
     */
    public function show(UjianHarian $ujian)
    {
        $siswa = Auth::user()->siswa;
        $this->authorizeSiswa($ujian, $siswa);

        $hasil = HasilUjian::where('ujian_harian_id', $ujian->id)->where('siswa_id', $siswa->id)->first();

        if (! $hasil) {
            $jumlahSoal = $ujian->soalUjians()->count();

            return view('siswa.ujian.mulai', compact('ujian', 'jumlahSoal'));
        }

        return redirect()->route('siswa.ujian.kerjakan', $ujian);
    }

    public function mulai(UjianHarian $ujian)
    {
        $siswa = Auth::user()->siswa;
        $this->authorizeSiswa($ujian, $siswa);

        $this->ujianService->mulai($ujian, $siswa);

        return redirect()->route('siswa.ujian.kerjakan', $ujian);
    }

    public function kerjakan(UjianHarian $ujian)
    {
        $siswa = Auth::user()->siswa;
        $this->authorizeSiswa($ujian, $siswa);

        $hasil = HasilUjian::where('ujian_harian_id', $ujian->id)->where('siswa_id', $siswa->id)->first();

        if (! $hasil) {
            return redirect()->route('siswa.ujian.show', $ujian);
        }

        if ($hasil->status !== 'sedang_mengerjakan' && $hasil->sudahHabisWaktu() === false) {
            // sudah selesai/menunggu penilaian -> tampilkan halaman hasil, bukan form pengerjaan
            return redirect()->route('siswa.ujian.selesai', $ujian);
        }

        if ($hasil->status === 'sedang_mengerjakan' && $hasil->sudahHabisWaktu()) {
            $this->ujianService->submitAkhir($ujian, $siswa, $hasil, otomatis: true);

            return redirect()->route('siswa.ujian.selesai', $ujian)
                ->with('error', 'Waktu pengerjaan sudah habis. Ujian otomatis dikumpulkan.');
        }

        $soalList = $ujian->soalUjians()->orderBy('urutan')->get();
        $jawabanList = JawabanUjian::whereIn('soal_ujian_id', $soalList->pluck('id'))
            ->where('siswa_id', $siswa->id)
            ->get()
            ->keyBy('soal_ujian_id');

        return view('siswa.ujian.kerjakan', compact('ujian', 'soalList', 'jawabanList', 'hasil'));
    }

    public function jawabStore(Request $request, UjianHarian $ujian, SoalUjian $soal)
    {
        $siswa = Auth::user()->siswa;
        $this->authorizeSiswa($ujian, $siswa);
        abort_if($soal->ujian_harian_id !== $ujian->id, 404);

        $hasil = HasilUjian::where('ujian_harian_id', $ujian->id)->where('siswa_id', $siswa->id)->firstOrFail();

        $payload = [];

        if ($soal->tipe_soal === 'pilihan_ganda') {
            $request->validate(['jawaban_teks' => ['required', 'in:A,B,C,D']]);
            $payload['jawaban_teks'] = $request->jawaban_teks;
        } else {
            $request->validate(['jawaban_teks' => ['required', 'string']]);
            $payload['jawaban_teks'] = $request->jawaban_teks;
        }

        $pesanError = $this->ujianService->simpanJawaban($soal, $siswa, $hasil, $payload);

        if ($pesanError) {
            return redirect()->route('siswa.ujian.selesai', $ujian)->with('error', $pesanError);
        }

        return back()->with('success', 'Jawaban tersimpan.');
    }

    public function submitAkhir(UjianHarian $ujian)
    {
        $siswa = Auth::user()->siswa;
        $this->authorizeSiswa($ujian, $siswa);

        $hasil = HasilUjian::where('ujian_harian_id', $ujian->id)->where('siswa_id', $siswa->id)->firstOrFail();
        $this->ujianService->submitAkhir($ujian, $siswa, $hasil);

        return redirect()->route('siswa.ujian.selesai', $ujian)->with('success', 'Ujian berhasil dikumpulkan.');
    }

    public function selesai(UjianHarian $ujian)
    {
        $siswa = Auth::user()->siswa;
        $this->authorizeSiswa($ujian, $siswa);

        $hasil = HasilUjian::where('ujian_harian_id', $ujian->id)->where('siswa_id', $siswa->id)->firstOrFail();

        return view('siswa.ujian.selesai', compact('ujian', 'hasil'));
    }

    private function authorizeSiswa(UjianHarian $ujian, $siswa): void
    {
        abort_if(! $siswa || $ujian->kelas_id !== $siswa->kelas_id || ! $ujian->is_published, 403);
    }
}
