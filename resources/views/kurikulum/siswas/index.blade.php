@extends('layouts.kurikulum')

@section('title', 'Data Siswa')

@section('kurikulum-content')
    <div class="ews-crud">
        <div class="ews-crud-toolbar">
            <h1 class="text-lg font-semibold">Daftar Siswa</h1>
            <div class="ews-actions">
                <a href="{{ route('kurikulum.siswas.import.form') }}"
                    class="ews-button ews-button-secondary">
                    Import Excel
                </a>
                <a href="{{ route('kurikulum.siswas.create') }}" class="ews-button ews-button-primary">
                    + Tambah Siswa
                </a>
            </div>
        </div>

        <p class="ews-table-hint md:hidden">Geser tabel ke samping untuk melihat seluruh kolom.</p>
        <div class="ews-table-scroll" role="region" aria-label="Tabel data" tabindex="0"><table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b">
                    <th scope="col" class="py-2">NIS</th>
                    <th scope="col" class="py-2">Nama</th>
                    <th scope="col" class="py-2">Kelas</th>
                    <th scope="col" class="py-2">L/P</th>
                    <th scope="col" class="py-2">Email Login</th>
                    <th scope="col" class="py-2">Password Default</th>
                    <th scope="col" class="py-2">Status Akun</th>
                    <th scope="col" class="py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($siswas as $siswa)
                    <tr class="border-b">
                        <td class="py-2">{{ $siswa->nis }}</td>
                        <td class="py-2">{{ $siswa->nama }}</td>
                        <td class="py-2">{{ $siswa->kelas->nama ?? '-' }}</td>
                        <td class="py-2">{{ $siswa->jenis_kelamin }}</td>
                        <td class="py-2">{{ $siswa->user->email }}</td>
                        <td class="py-2 text-gray-500">password123</td>
                        <td class="py-2">
                            @if ($siswa->user->is_active)
                                <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded">Aktif</span>
                            @else
                                <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded">Nonaktif</span>
                            @endif
                        </td>
                        <td class="py-2">
                            <a href="{{ route('kurikulum.siswas.edit', $siswa) }}" class="ews-button ews-button-secondary">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-4 text-center text-gray-500">Belum ada data siswa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table></div>

        <div class="mt-4">
            {{ $siswas->links() }}
        </div>
    </div>
@endsection
