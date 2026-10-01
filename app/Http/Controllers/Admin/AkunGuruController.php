<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AkunGuruController extends Controller
{
    public function index()
    {
        $gurus = Guru::with(['user.roles'])
            ->withCount(['kelasSebagaiWali', 'mapels', 'kelasBk'])
            ->orderBy('nama')
            ->paginate(10);

        $gurus->getCollection()->transform(function (Guru $guru) {
            $guru->role_yang_kurang = $this->cekRoleYangKurang($guru);

            return $guru;
        });

        return view('admin.akun_guru.index', compact('gurus'));
    }

    public function editRole(Guru $guru)
    {
        $guru->load('user.roles');
        $roles = Role::orderBy('name')->get();

        return view('admin.akun_guru.edit_role', compact('guru', 'roles'));
    }

    public function updateRole(Request $request, Guru $guru)
    {
        $validated = $request->validate([
            'role_ids' => ['required', 'array', 'min:1'],
            'role_ids.*' => ['exists:roles,id'],
            'default_role_id' => ['required', 'exists:roles,id'],
        ]);

        if (! in_array($validated['default_role_id'], $validated['role_ids'])) {
            return back()->withInput()->with('error', 'Role default harus salah satu dari role yang dicentang.');
        }

        $guru->user->roles()->sync($validated['role_ids']);
        $guru->user->update(['default_role_id' => $validated['default_role_id']]);

        return redirect()
            ->route('admin.akun-guru.index')
            ->with('success', "Role untuk {$guru->nama} berhasil diperbarui.");
    }

    public function toggleAktif(Guru $guru)
    {
        $guru->user->update(['is_active' => ! $guru->user->is_active]);

        $status = $guru->user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Akun {$guru->nama} berhasil {$status}.");
    }

    public function resetPassword(Guru $guru)
    {
        $guru->user->update(['password' => Hash::make(config('school.default_password'))]);

        return back()->with('success', "Password {$guru->nama} berhasil direset ke default.");
    }

    /**
     * Cek role apa yang "seharusnya" dimiliki guru ini berdasarkan penugasan
     * dari Kurikulum, tapi belum di-assign Admin.
     *
     * @return array<int, string>
     */
    private function cekRoleYangKurang(Guru $guru): array
    {
        $roleDimiliki = $guru->user->roles->pluck('name')->toArray();
        $kurang = [];

        if ($guru->kelas_sebagai_wali_count > 0 && ! in_array('wali_kelas', $roleDimiliki)) {
            $kurang[] = 'Wali Kelas';
        }

        if ($guru->mapels_count > 0 && ! in_array('guru_mapel', $roleDimiliki)) {
            $kurang[] = 'Guru Mapel';
        }

        if ($guru->kelas_bk_count > 0 && ! in_array('guru_bk', $roleDimiliki)) {
            $kurang[] = 'Guru BK';
        }

        return $kurang;
    }
}
