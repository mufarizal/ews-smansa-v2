@extends('layouts.guru_mapel')

@section('title', 'Tugas')

@section('guru-mapel-content')
    <section class="ews-crud" aria-labelledby="tugas-heading">
        <header class="ews-crud-toolbar">
            <div>
                <h1 id="tugas-heading">Tugas</h1>
                <p class="mt-2 text-sm text-gray-600">Kelola tugas, soal, dan penilaian kelas Anda.</p>
            </div>
            <a href="{{ route('guru_mapel.tugas.create') }}" class="ews-button ews-button-primary">+ Buat Tugas</a>
        </header>

        <p class="ews-table-hint md:hidden">Geser tabel ke samping untuk melihat seluruh kolom.</p>
        <div class="ews-table-scroll" role="region" aria-label="Daftar tugas" tabindex="0">
            <table>
                <thead>
                    <tr>
                        <th scope="col">Judul</th>
                        <th scope="col">Mapel</th>
                        <th scope="col">Kelas</th>
                        <th scope="col">Deadline</th>
                        <th scope="col">Status</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tugasList as $tugas)
                        <tr>
                            <td class="min-w-48 font-semibold break-words">{{ $tugas->judul }}</td>
                            <td>{{ $tugas->mapel->nama }}</td>
                            <td class="whitespace-nowrap">{{ $tugas->kelas->nama }}</td>
                            <td class="whitespace-nowrap">{{ $tugas->tanggal_selesai->translatedFormat('d M Y') }}</td>
                            <td>
                                <span class="inline-flex rounded px-2.5 py-1 text-xs font-medium {{ $tugas->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $tugas->is_published ? 'Dipublikasi' : 'Draft' }}
                                </span>
                            </td>
                            <td><a href="{{ route('guru_mapel.tugas.show', $tugas) }}" class="ews-button ews-button-secondary" aria-label="Detail tugas {{ $tugas->judul }}">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6">Belum ada tugas. Pilih “Buat Tugas” untuk menambahkan tugas pertama.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-5">{{ $tugasList->links() }}</div>
    </section>
@endsection
