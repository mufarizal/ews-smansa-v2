<?php

namespace App\Http\Controllers\Kurikulum;

use App\Http\Controllers\Controller;
use App\Models\KehadiranGuru;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class KehadiranGuruController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());

        $guruIdsQuery = KehadiranGuru::where('tanggal', $tanggal)
            ->select('guru_id')
            ->distinct()
            ->orderBy('guru_id');

        $guruIds = $guruIdsQuery->paginate(20)->withQueryString();

        $kehadirans = KehadiranGuru::with('guru')
            ->where('tanggal', $tanggal)
            ->whereIn('guru_id', $guruIds->pluck('guru_id'))
            ->orderBy('guru_id')
            ->orderBy('sesi')
            ->get()
            ->groupBy('guru_id');

        return view('kurikulum.kehadiran_guru.index', compact('kehadirans', 'tanggal', 'guruIds'));
    }

    public function edit(KehadiranGuru $kehadiranGuru)
    {
        return view('kurikulum.kehadiran_guru.edit', compact('kehadiranGuru'));
    }

    public function update(Request $request, KehadiranGuru $kehadiranGuru)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:hadir,izin,sakit,alpha'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $kehadiranGuru->update($validated);

        return redirect()
            ->route('kurikulum.kehadiran-guru.index', ['tanggal' => $kehadiranGuru->tanggal->toDateString()])
            ->with('success', 'Kehadiran guru berhasil diperbarui.');
    }
}
