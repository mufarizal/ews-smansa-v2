@extends('layouts.kurikulum')

@section('title', 'Mata Pelajaran')

@section('kurikulum-content')
    <div class="ews-crud">
        <div class="ews-crud-toolbar">
            <h1 class="text-lg font-semibold">Daftar Mata Pelajaran</h1>
            <a href="{{ route('kurikulum.mapels.create') }}" class="ews-button ews-button-primary">
                + Tambah Mapel
            </a>
        </div>

        <p class="ews-table-hint md:hidden">Geser tabel ke samping untuk melihat seluruh kolom.</p>
        <div class="ews-table-scroll" role="region" aria-label="Tabel data" tabindex="0"><table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b">
                    <th scope="col" class="py-2">Nama</th>
                    <th scope="col" class="py-2">Kode</th>
                    <th scope="col" class="py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mapels as $mapel)
                    <tr class="border-b">
                        <td class="py-2">{{ $mapel->nama }}</td>
                        <td class="py-2">{{ $mapel->kode ?? '-' }}</td>
                        <td class="py-2 space-x-2">
                            <a href="{{ route('kurikulum.mapels.edit', $mapel) }}" class="ews-button ews-button-secondary">Edit</a>
                            <form action="{{ route('kurikulum.mapels.destroy', $mapel) }}" method="POST" class="inline"
                                onsubmit="return confirm('Yakin hapus mapel ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ews-button ews-button-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-4 text-center text-gray-500">Belum ada mata pelajaran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table></div>

        <div class="mt-4">
            {{ $mapels->links() }}
        </div>
    </div>
@endsection
