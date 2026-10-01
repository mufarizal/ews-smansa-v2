@extends('layouts.admin')

@section('title', 'Kelola Akun Siswa')

@section('admin-content')
    <div class="ews-crud">
        <div class="ews-crud-toolbar">
            <h1 class="text-lg font-semibold">Kelola Akun Siswa</h1>
            <form method="GET" action="{{ route('admin.akun-siswa.index') }}" class="ews-filter">
                <div><label for="filter-cari">Cari nama atau NIS</label>
                <input type="text" id="filter-cari" name="cari" value="{{ request('cari') }}" placeholder="Cari nama/NIS..."
                    class="border rounded px-3 py-2 text-sm">
            </div><button type="submit" class="ews-button ews-button-secondary">Terapkan</button></form>
        </div>

        <p class="ews-table-hint md:hidden">Geser tabel ke samping untuk melihat seluruh kolom.</p>
        <div class="ews-table-scroll" role="region" aria-label="Tabel data" tabindex="0"><table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b">
                    <th scope="col" class="py-2">NIS</th>
                    <th scope="col" class="py-2">Nama</th>
                    <th scope="col" class="py-2">Kelas</th>
                    <th scope="col" class="py-2">Email Login</th>
                    <th scope="col" class="py-2">Password Default</th>
                    <th scope="col" class="py-2">Status</th>
                    <th scope="col" class="py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($siswas as $siswa)
                    <tr class="border-b">
                        <td class="py-2">{{ $siswa->nis }}</td>
                        <td class="py-2">{{ $siswa->nama }}</td>
                        <td class="py-2">{{ $siswa->kelas->nama ?? '-' }}</td>
                        <td class="py-2">{{ $siswa->user->email }}</td>
                        <td class="py-2 text-gray-500">password123</td>
                        <td class="py-2">
                            @if ($siswa->user->is_active)
                                <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded">Aktif</span>
                            @else
                                <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded">Nonaktif</span>
                            @endif
                        </td>
                        <td class="py-2 space-x-2">
                            <form action="{{ route('admin.akun-siswa.toggle-aktif', $siswa) }}" method="POST"
                                class="inline" onsubmit="return confirm('Yakin ubah status akun ini?')">
                                @csrf
                                <button type="submit" class="ews-button ews-button-secondary">
                                    {{ $siswa->user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.akun-siswa.reset-password', $siswa) }}" method="POST"
                                class="inline" onsubmit="return confirm('Reset password ke default?')">
                                @csrf
                                <button type="submit" class="ews-button ews-button-secondary">Reset Password</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-4 text-center text-gray-500">Belum ada data siswa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table></div>

        <div class="mt-4">
            {{ $siswas->links() }}
        </div>
    </div>
@endsection
