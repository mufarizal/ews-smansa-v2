<?php

namespace App\Http\Controllers\Kurikulum;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $gurus = Guru::with('user')->orderBy('nama')->paginate(10);

        return view('kurikulum.gurus.index', compact('gurus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('kurikulum.gurus.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'required|string|max:30|unique:gurus,nip',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $email = $validated['nip'].'@'.config('school.email_domain_guru');

            $user = User::create([
                'name' => $validated['nama'],
                'email' => $email,
                'password' => Hash::make(config('school.default_user_password')),
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            Guru::create([
                ...$validated,
                'user_id' => $user->id,
            ]);
        });

        return redirect()->route('kurikulum.gurus.index')->with('success', 'Guru berhasil ditambahkan. Akun Login: '.$validated['nip'].'@'.config('school.email_domain_guru'));
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
    public function edit(Guru $guru)
    {
        return view('kurikulum.gurus.edit', compact('guru'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Guru $guru)
    {
        $validated = $request->validate([
            'nip' => 'sometimes|string|max:30|unique:gurus,nip,'.$guru->id,
            'nama' => 'sometimes|string|max:255',
            'jenis_kelamin' => 'sometimes|in:L,P',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $guru) {
            $nipBerubah = $guru->nip !== $validated['nip'];

            $guru->update($validated);
            $guru->user->update([
                'name' => $validated['nama'],
                'email' => $nipBerubah ?
                    $validated['nip'].'@'.config('school.email_domain_guru')
                    : $guru->user->email,
            ]);
        });

        return redirect()->route('kurikulum.gurus.index')->with('success', 'Guru Berhasil Diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guru $guru)
    {
        //
    }
}
