@extends('layouts.kurikulum')

@section('title', 'Semester')

@section('kurikulum-content')
    <div class="ews-crud">
        <div class="ews-crud-toolbar">
            <h1 class="text-lg font-semibold">Daftar Semester</h1>
            <a href="{{ route('kurikulum.semesters.create') }}" class="ews-button ews-button-primary">
                + Tambah Semester
            </a>
        </div>

        <p class="ews-table-hint md:hidden">Geser tabel ke samping untuk melihat seluruh kolom.</p>
        <div class="ews-table-scroll" role="region" aria-label="Tabel data" tabindex="0"><table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b">
                    <th scope="col" class="py-2">Nama</th>
                    <th scope="col" class="py-2">Tahun Ajaran</th>
                    <th scope="col" class="py-2">Periode</th>
                    <th scope="col" class="py-2">Status</th>
                    <th scope="col" class="py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($semesters as $semester)
                    <tr class="border-b">
                        <td class="py-2">{{ $semester->nama }}</td>
                        <td class="py-2">{{ $semester->tahun_ajaran }} ({{ ucfirst($semester->jenis) }})</td>
                        <td class="py-2">
                            {{ $semester->tanggal_mulai->format('Y-m-d') }} -
                            {{ $semester->tanggal_selesai->format('Y-m-d') }}
                        </td>
                        <td class="py-2">
                            @if ($semester->is_aktif)
                                <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded">Aktif</span>
                            @else
                                <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded">Nonaktif</span>
                            @endif
                        </td>
                        <td class="py-2 space-x-2">
                            @if ($semester->is_aktif)
                                <form action="{{ route('kurikulum.semesters.nonaktifkan', $semester) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    <button type="submit" class="ews-button ews-button-secondary">Nonaktifkan</button>
                                </form>
                            @else
                                <form action="{{ route('kurikulum.semesters.aktifkan', $semester) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    <button type="submit" class="ews-button ews-button-secondary">Aktifkan</button>
                                </form>
                            @endif
                            <a href="{{ route('kurikulum.semesters.edit', $semester) }}"
                                class="ews-button ews-button-secondary">Edit</a>
                            <form action="{{ route('kurikulum.semesters.destroy', $semester) }}" method="POST"
                                class="inline" onsubmit="return confirm('Yakin hapus semester ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ews-button ews-button-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-4 text-center text-gray-500">Belum ada semester.</td>
                    </tr>
                @endforelse
            </tbody>
        </table></div>

        <div class="mt-4">
            {{ $semesters->links() }}
        </div>
    </div>
@endsection
