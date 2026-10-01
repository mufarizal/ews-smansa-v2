<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Services\EwsSnapshotService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(private EwsSnapshotService $ewsSnapshot) {}

    public function index()
    {
        $siswa = Auth::user()->siswa;

        $hasilTerkini = $this->ewsSnapshot->latestResult($siswa);
        $trend = $this->ewsSnapshot->trend($siswa);
        $rekomendasi = $this->ewsSnapshot->rekomendasi($siswa, 'siswa');

        return view('siswa.dashboard', compact('hasilTerkini', 'trend', 'rekomendasi'));
    }
}
