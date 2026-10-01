<?php

namespace App\Http\Controllers\GuruBk;

use App\Exports\MonitoringSiswaExport;
use App\Http\Controllers\Controller;
use App\Models\AiRecommendationFeedback;
use App\Models\Kelas;
use App\Models\PerilakuSiswa;
use App\Models\Siswa;
use App\Services\EwsSnapshotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class MonitoringController extends Controller
{
    public function __construct(private EwsSnapshotService $ewsSnapshot) {}

    public function index()
    {
        $guru = Auth::user()->guru;
        $kelasList = $guru->kelasBk;

        $ringkasanPerKelas = $kelasList->mapWithKeys(
            fn (Kelas $kelas) => [$kelas->id => $this->ewsSnapshot->ringkasanKelas($kelas)]
        );

        return view('guru_bk.monitoring.index', compact('kelasList', 'ringkasanPerKelas'));
    }

    public function show(Kelas $kelas)
    {
        $this->authorizeKelas($kelas);

        $siswas = $this->ewsSnapshot->siswaUrutPrioritas($kelas);

        return view('guru_bk.monitoring.show', compact('kelas', 'siswas'));
    }

    public function showSiswa(Kelas $kelas, Siswa $siswa)
    {
        $this->authorizeKelas($kelas);
        abort_if($siswa->kelas_id !== $kelas->id, 404);

        $hasilTerkini = $this->ewsSnapshot->latestResult($siswa);
        $trend = $this->ewsSnapshot->trend($siswa);
        $rekomendasi = $this->ewsSnapshot->rekomendasi($siswa, 'guru');

        $riwayatPerilaku = PerilakuSiswa::with('perilaku')
            ->where('siswa_id', $siswa->id)
            ->orderByDesc('tanggal')
            ->limit(20)
            ->get();

        return view('guru_bk.monitoring.show_siswa', compact(
            'kelas',
            'siswa',
            'hasilTerkini',
            'trend',
            'rekomendasi',
            'riwayatPerilaku'
        ));
    }

    public function storeFeedback(Request $request, Kelas $kelas, Siswa $siswa)
    {
        $this->authorizeKelas($kelas);
        abort_if($siswa->kelas_id !== $kelas->id, 404);

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

        return back()->with('success', 'Masukan berhasil dikirim. Rekomendasi akan diperbarui otomatis dalam beberapa menit.');
    }

    public function export(Kelas $kelas)
    {
        $this->authorizeKelas($kelas);

        return Excel::download(new MonitoringSiswaExport($kelas, $this->ewsSnapshot), 'monitoring-'.$kelas->nama.'.xlsx');
    }

    private function authorizeKelas(Kelas $kelas): void
    {
        $guru = Auth::user()->guru;
        abort_if(! $guru->kelasBk->pluck('id')->contains($kelas->id), 403);
    }
}
