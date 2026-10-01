<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Services\KehadiranGuruService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(private KehadiranGuruService $kehadiranService) {}

    public function index()
    {
        $guru = Auth::user()->guru;
        $kehadiran = $this->kehadiranService->kehadiranHariIni($guru);

        return view('wali_kelas.dashboard', compact('kehadiran'));
    }

    public function kehadiran(Request $request)
    {
        $validated = $request->validate([
            'sesi' => 'required|in:pagi,sore',
        ]);
        $guru = Auth::user()->guru;
        $record = $this->kehadiranService->catat($guru, $validated['sesi']);

        return back()->with('success', 'Kehadiran '.ucfirst($validated['sesi']).'Tercatat Pukul'.$record->waktu_absen->format('H:i'));
    }
}
