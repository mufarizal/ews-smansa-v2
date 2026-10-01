@extends('layouts.kurikulum')

@section('title', 'Data Guru')

@section('kurikulum-content')
    <div class="ews-crud">
        <div class="ews-crud-toolbar">
            <h1 class="text-lg font-semibold">Daftar Guru</h1>
            <div class="ews-actions">
                <a href="{{ route('kurikulum.gurus.import.form') }}"
                    class="ews-button ews-button-secondary">
                    Import Excel
                </a>
                <a href="{{ route('kurikulum.gurus.create') }}" class="ews-button ews-button-primary">
                    + Tambah Guru
                </a>
            </div>
        </div>

        <p class="ews-table-hint md:hidden">Geser tabel ke samping untuk melihat seluruh kolom.</p>
        <div class="ews-table-scroll" role="region" aria-label="Tabel data" tabindex="0"><table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b">
                    <th scope="col" class="py-2">NIP</th>
                    <th scope="col" class="py-2">Nama</th>
                    <th scope="col" class="py-2">L/P</th>
                    <th scope="col" class="py-2">Email Login</th>
                    <th scope="col" class="py-2">Status Akun</th>
                    <th scope="col" class="py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($gurus as $guru)
                    <tr class="border-b">
                        <td class="py-2">{{ $guru->nip }}</td>
                        <td class="py-2">{{ $guru->nama }}</td>
                        <td class="py-2">{{ $guru->jenis_kelamin }}</td>
                        <td class="py-2">{{ $guru->user->email }}</td>
                        <td class="py-2">
                            @if ($guru->user->is_active)
                                <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded">Aktif</span>
                            @else
                                <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded">Nonaktif</span>
                            @endif
                        </td>
                        <td class="py-2">
                            <a href="{{ route('kurikulum.gurus.edit', $guru) }}" class="ews-button ews-button-secondary">Edit</a>
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
