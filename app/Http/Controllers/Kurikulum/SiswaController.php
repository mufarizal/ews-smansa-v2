<?php

namespace App\Http\Controllers\Kurikulum;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $siswas = Siswa::with(['user', 'kelas'])->orderBy('nama')->paginate(10);

        return view('kurikulum.siswas.index', compact('siswas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama')->get();

        return view('kurikulum.siswas.create', compact('kelas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'required|string|max:30|unique:siswas,nis',
            'nama' => 'required|string|max:150',
            'jenis_kelamin' => 'required|in:L,P',
            'kelas_id' => 'nullable|exists:kelas,id',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'nama_orang_tua' => 'nullable|string|max:150',
            'no_hp_orang_tua' => 'nullable|string|max:20',
        ]);

        DB::transaction(function () use ($validated) {
            $email = $validated['nis'].'@'.config('school.email_domain_siswa');
            $roleSiswa = Role::where('name', 'siswa')->firstOrFail();

            $user = User::create([
                'name' => $validated['nama'],
                'email' => $email,
                'password' => Hash::make(config('school.default_password')),
                'is_active' => true,
                'default_role_id' => $roleSiswa->id,
                'email_verified_at' => now(),
            ]);

            $user->roles()->attach($roleSiswa->id);

            Siswa::create([
                ...$validated,
                'user_id' => $user->id,
            ]);
        });

        return redirect()->route('kurikulum.siswas.index')->with('success', 'Siswa Berhasil Ditambahkan. Email Login: '.$validated['nis'].'@'.config('school.email_domain_siswa'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Siswa $siswa)
    {
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama')->get();

        return view('kurikulum.siswas.edit', compact('siswa', 'kelas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Siswa $siswa)
    {
        $validated = $request->validate([
            'nis' => 'sometimes|string|max:30|unique:siswas,nis,'.$siswa->id,
            'nama' => 'sometimes|string|max:150',
            'jenis_kelamin' => 'sometimes|in:L,P',
            'kelas_id' => 'nullable|exists:kelas,id',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'nama_orang_tua' => 'nullable|string|max:150',
            'no_hp_orang_tua' => 'nullable|string|max:20',
        ]);

        DB::transaction(function () use ($validated, $siswa) {
            $nisBerubah = $validated['nis'] !== $siswa->nis;

            $siswa->update($validated);
            $siswa->user->update([
                'name' => $validated['nama'],
                'email' => $nisBerubah ? $validated['nis'].'@'.config('school.email_domain_siswa') : $siswa->user->email,
            ]);
        });

        return redirect()->route('kurikulum.siswas.index')->with('success', 'Data Siswa Berhasil Diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
