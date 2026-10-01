<?php

namespace App\Http\Controllers\WaliKelas;

use App\Exports\WaliKelasSiswaExport;
use App\Http\Controllers\Controller;
use App\Models\AiRecommendationFeedback;
use App\Models\Kelas;
use App\Models\PerilakuSiswa;
use App\Models\Siswa;
use App\Services\EwsSnapshotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class KelasSayaController extends Controller
{
    public function __construct(private EwsSnapshotService $ewsSnapshot) {}

    public function index(Request $request)
    {
        $guru = Auth::user()->guru;
        $kelasList = $guru->kelasSebagaiWali;

        $kelasTerpilih = $request->filled('kelas_id')
            ? $kelasList->firstWhere('id', (int) $request->kelas_id)
            : $kelasList->first();

        $siswas = $kelasTerpilih ? $this->ewsSnapshot->siswaUrutPrioritas($kelasTerpilih) : collect();

        return view('wali_kelas.kelas_saya.index', compact('kelasList', 'kelasTerpilih', 'siswas'));
    }

    public function showSiswa(Siswa $siswa)
    {
        $this->authorizeSiswa($siswa);

        $hasilTerkini = $this->ewsSnapshot->latestResult($siswa);
        $trend = $this->ewsSnapshot->trend($siswa);
        $rekomendasi = $this->ewsSnapshot->rekomendasi($siswa, 'guru');

        $riwayatPerilaku = PerilakuSiswa::with('perilaku')
            ->where('siswa_id', $siswa->id)
            ->orderByDesc('tanggal')
            ->limit(20)
            ->get();

        return view('wali_kelas.kelas_saya.show_siswa', compact(
            'siswa',
            'hasilTerkini',
            'trend',
            'rekomendasi',
            'riwayatPerilaku'
        ));
    }

    public function storeFeedback(Request $request, Siswa $siswa)
    {
        $this->authorizeSiswa($siswa);

        $validated = $request->validate([
            'catatan' => ['required', 'string'],
        ]);

        $rekomendasi = $this->ewsSnapshot->rekomendasi($siswa, 'guru');

        if (! $rekomendasi) {
            return back()->with('error', 'Belum ada rekomendasi AI untuk siswa ini.');
        }

        AiRecommendationFeedback::create([
            'ai_recommendation_id' => $rekomendasi->id,
            'guru_id' => Auth::user()->guru->id,
            'catatan' => $validated['catatan'],
            'status' => 'menunggu',
        ]);

        return back()->with('success', 'Masukan berhasil dikirim.');
    }

    public function export(Kelas $kelas)
    {
        $this->authorizeKelas($kelas);

        return Excel::download(new WaliKelasSiswaExport($kelas, $this->ewsSnapshot), 'kelas-saya-'.$kelas->nama.'.xlsx');
    }

    private function authorizeKelas(Kelas $kelas): void
    {
        $guru = Auth::user()->guru;
        abort_if(! $guru->kelasSebagaiWali->pluck('id')->contains($kelas->id), 403);
    }

    private function authorizeSiswa(Siswa $siswa): void
    {
        $guru = Auth::user()->guru;
        abort_if(! $guru->kelasSebagaiWali->pluck('id')->contains($siswa->kelas_id), 403);
    }
}
