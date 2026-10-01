@extends('layouts.kurikulum')

@section('title', 'Penugasan Guru BK')

@section('kurikulum-content')
    <div class="ews-crud">
        <div class="ews-crud-toolbar">
            <h1 class="text-lg font-semibold">Penugasan Guru BK</h1>
            <a href="{{ route('kurikulum.guru-bk-kelas.create') }}" class="ews-button ews-button-primary">
                + Tambah Penugasan
            </a>
        </div>

        <p class="ews-table-hint md:hidden">Geser tabel ke samping untuk melihat seluruh kolom.</p>
        <div class="ews-table-scroll" role="region" aria-label="Tabel data" tabindex="0"><table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b">
                    <th scope="col" class="py-2">Guru BK</th>
                    <th scope="col" class="py-2">Kelas</th>
                    <th scope="col" class="py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($penugasans as $item)
                    <tr class="border-b">
                        <td class="py-2">{{ $item->guru->nama }}</td>
                        <td class="py-2">{{ $item->kelas->nama }}</td>
                        <td class="py-2">
                            <form action="{{ route('kurikulum.guru-bk-kelas.destroy', $item) }}" method="POST"
                                class="inline" onsubmit="return confirm('Yakin hapus penugasan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ews-button ews-button-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-4 text-center text-gray-500">Belum ada penugasan guru BK.</td>
                    </tr>
                @endforelse
            </tbody>
        </table></div>

        <div class="mt-4">
            {{ $penugasans->links() }}
        </div>
    </div>
@endsection
