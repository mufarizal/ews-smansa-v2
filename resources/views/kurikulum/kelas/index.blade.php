@extends('layouts.kurikulum')

@section('title', 'Kelas')

@section('kurikulum-content')
    <div class="ews-crud">
        <div class="ews-crud-toolbar">
            <h1 class="text-lg font-semibold">Daftar Kelas</h1>
            <a href="{{ route('kurikulum.kelas.create') }}" class="ews-button ews-button-primary">
                + Tambah Kelas
            </a>
        </div>

        <p class="ews-table-hint md:hidden">Geser tabel ke samping untuk melihat seluruh kolom.</p>
        <div class="ews-table-scroll" role="region" aria-label="Tabel data" tabindex="0"><table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b">
                    <th scope="col" class="py-2">Nama Kelas</th>
                    <th scope="col" class="py-2">Tingkat</th>
                    <th scope="col" class="py-2">Wali Kelas</th>
                    <th scope="col" class="py-2">Jumlah Siswa</th>
                    <th scope="col" class="py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kelas as $item)
                    <tr class="border-b">
                        <td class="py-2">{{ $item->nama }}</td>
                        <td class="py-2">{{ $item->tingkat }}</td>
                        <td class="py-2">{{ $item->waliKelas->nama ?? '-' }}</td>
                        <td class="py-2">{{ $item->siswas_count }}</td>
                        <td class="py-2 space-x-2">
                            <a href="{{ route('kurikulum.kelas.edit', $item) }}" class="ews-button ews-button-secondary">Edit</a>
                            <form action="{{ route('kurikulum.kelas.destroy', $item) }}" method="POST" class="inline"
                                onsubmit="return confirm('Yakin hapus kelas ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ews-button ews-button-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-4 text-center text-gray-500">Belum ada kelas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table></div>

        <div class="mt-4">
            {{ $kelas->links() }}
        </div>
    </div>
@endsection
