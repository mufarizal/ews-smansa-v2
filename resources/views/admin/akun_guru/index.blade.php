@extends('layouts.admin')

@section('title', 'Kelola Akun Guru')

@section('admin-content')
    <div class="ews-crud">
        <h1 class="text-lg font-semibold mb-4">Kelola Akun Guru</h1>

        <p class="ews-table-hint md:hidden">Geser tabel ke samping untuk melihat seluruh kolom.</p>
        <div class="ews-table-scroll" role="region" aria-label="Tabel data" tabindex="0"><table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b">
                    <th scope="col" class="py-2">Nama</th>
                    <th scope="col" class="py-2">NIP</th>
                    <th scope="col" class="py-2">Email Login</th>
                    <th scope="col" class="py-2">Role Saat Ini</th>
                    <th scope="col" class="py-2">Status</th>
                    <th scope="col" class="py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($gurus as $guru)
                    <tr class="border-b align-top">
                        <td class="py-2">{{ $guru->nama }}</td>
                        <td class="py-2">{{ $guru->nip }}</td>
                        <td class="py-2">{{ $guru->user->email }}</td>
                        <td class="py-2">
                            @forelse ($guru->user->roles as $role)
                                <span class="bg-blue-50 text-blue-700 text-xs px-2 py-1 rounded inline-block mb-1">
                                    {{ $role->label }}
                                    @if ($role->id === $guru->user->default_role_id)
                                        <strong>★</strong>
                                    @endif
                                </span>
                            @empty
                                <span class="text-gray-400 text-xs">Belum ada role</span>
                            @endforelse

                            @if (count($guru->role_yang_kurang) > 0)
                                <div class="mt-1">
                                    @foreach ($guru->role_yang_kurang as $roleKurang)
                                        <span
                                            class="bg-orange-50 text-orange-700 text-xs px-2 py-1 rounded inline-block mb-1">
                                            ⚠ Perlu role {{ $roleKurang }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="py-2">
                            @if ($guru->user->is_active)
                                <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded">Aktif</span>
                            @else
                                <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded">Nonaktif</span>
                            @endif
                        </td>
                        <td class="py-2 space-y-1">
                            <div>
                                <a href="{{ route('admin.akun-guru.role.edit', $guru) }}" class="ews-button ews-button-secondary">Atur
                                    Role</a>
                            </div>
                            <div>
                                <form action="{{ route('admin.akun-guru.toggle-aktif', $guru) }}" method="POST"
                                    class="inline" onsubmit="return confirm('Yakin ubah status akun ini?')">
                                    @csrf
                                    <button type="submit" class="ews-button ews-button-secondary">
                                        {{ $guru->user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </div>
                            <div>
                                <form action="{{ route('admin.akun-guru.reset-password', $guru) }}" method="POST"
                                    class="inline" onsubmit="return confirm('Reset password ke default?')">
                                    @csrf
                                    <button type="submit" class="ews-button ews-button-secondary">Reset Password</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-4 text-center text-gray-500">Belum ada data guru.</td>
                    </tr>
                @endforelse
            </tbody>
        </table></div>

        <div class="mt-4">
            {{ $gurus->links() }}
        </div>
    </div>
@endsection
