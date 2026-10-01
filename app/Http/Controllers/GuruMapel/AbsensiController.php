<?php

namespace App\Http\Controllers\GuruMapel;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Semester;
use App\Services\AbsensiService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    private const HARI_INDONESIA = [
        1 => 'senin',
        2 => 'selasa',
        3 => 'rabu',
        4 => 'kamis',
        5 => 'jumat',
        6 => 'sabtu',
        0 => 'minggu',
    ];

    public function __construct(private AbsensiService $absensiService) {}

    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());

        $request->merge(['tanggal' => $tanggal]);
        $request->validate([
            'tanggal' => ['date', 'after_or_equal:'.$this->absensiService->tanggalMinimal(), 'before_or_equal:'.$this->absensiService->tanggalMaksimal()],
        ]);
        $guru = Auth::user()->guru;
        $namaHari = self::HARI_INDONESIA[Carbon::parse($tanggal)->dayOfWeek];
        $semesterAktif = Semester::aktif();

        $jadwals = collect();

        if ($namaHari !== 'minggu' && $semesterAktif) {
            $jadwals = Jadwal::with(['kelas', 'mapel'])
                ->where('guru_id', $guru->id)->where('hari', $namaHari)
                ->where('semester_id', $semesterAktif->id)->get()
                ->map(function (Jadwal $jadwal) use ($tanggal) {
                    $jumlahSiswa = $jadwal->kelas->siswas()->count();
                    $jumlahTerisi = $jadwal->absensis()->where('tanggal', $tanggal)->count();
                    $jadwal->status_input = $jumlahSiswa > 0 && $jumlahSiswa >= $jumlahSiswa ? 'lengkap'
                        : ($jumlahTerisi > 0 ? 'sebagian' : 'belum');

                    return $jadwal;
                });
        }

        return view('guru_mapel.absensi.index', compact('jadwals', 'tanggal', 'namaHari'));
    }

    public function show(Request $request, Jadwal $jadwal)
    {
        abort_if($jadwal->guru_id !== Auth::user()->guru->id, 403);

        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        $siswas = $jadwal->kelas->siswas()->orderBy('nama')->get();
        $absensiSudahAda = $jadwal->absensis()->where('tanggal', $tanggal)->get()->keyBy('siswa_id');

        return view('guru_mapel.absensi.show', compact('jadwal', 'siswas', 'absensiSudahAda', 'tanggal'));
    }

    public function store(Request $request, Jadwal $jadwal)
    {
        abort_if($jadwal->guru_id !== Auth::user()->guru->id, 403);

        $validated = $request->validate([
            'tanggal' => ['required', 'date', 'after_or_equal:'.$this->absensiService->tanggalMinimal(), 'before_or_equal:'.$this->absensiService->tanggalMaksimal()],
            'data' => ['required', 'array'],
            'data.*.status' => ['required', 'in:hadir,izin,sakit,alpha,terlambar'],
            'data.*.menit_terlambat' => ['nullable', 'integer', 'min:1'],
        ]);

        foreach ($validated['data'] as $siswaId => $item) {
            if ($item['status'] === 'terlambat' && empty($item['menit_terlambat'])) {
                return back()->withInput()->with('error', 'Menit terlambat harus diisi jika status terlambat dipilih.');
            }
        }

        $this->absensiService->storeBulk($jadwal, $validated['tanggal'], $validated['data']);

        return redirect()->route('guru_mapel.absensi.index', ['tanggal' => $validated['tanggal']])->with('success', 'Absensi kelas '.$jadwal->kelas->nama.' berhasil disimpan.');
    }
}
