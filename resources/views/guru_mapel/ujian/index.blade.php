@extends('layouts.guru_mapel')

@section('title', 'Ujian Harian')

@section('guru-mapel-content')
    <section class="ews-crud" aria-labelledby="ujian-heading">
        <header class="ews-crud-toolbar">
            <div>
                <h1 id="ujian-heading">Ujian Harian</h1>
                <p class="mt-2 text-sm text-gray-600">Kelola jadwal ujian, soal, dan hasil belajar siswa dalam satu tempat.</p>
            </div>
            <a href="{{ route('guru_mapel.ujian.create') }}" class="ews-button ews-button-primary">+ Buat Ujian</a>
        </header>

        <p class="ews-table-hint md:hidden">Geser tabel ke samping untuk melihat seluruh kolom.</p>
        <div class="ews-table-scroll" role="region" aria-label="Daftar ujian" tabindex="0">
            <table>
                <thead>
                    <tr>
                        <th scope="col">Judul</th>
                        <th scope="col">Mapel</th>
                        <th scope="col">Kelas</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Durasi</th>
                        <th scope="col">Status</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ujianList as $ujian)
                        <tr>
                            <td class="min-w-48 font-semibold break-words">{{ $ujian->judul }}</td>
                            <td>{{ $ujian->mapel->nama }}</td>
                            <td class="whitespace-nowrap">{{ $ujian->kelas->nama }}</td>
                            <td class="whitespace-nowrap">{{ $ujian->tanggal->translatedFormat('d M Y') }}</td>
                            <td class="whitespace-nowrap tabular-nums">{{ $ujian->durasi_menit }} menit</td>
                            <td>
                                <span class="inline-flex rounded px-2.5 py-1 text-xs font-medium {{ $ujian->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $ujian->is_published ? 'Dipublikasi' : 'Draft' }}
                                </span>
                            </td>
                            <td><a href="{{ route('guru_mapel.ujian.show', $ujian) }}" class="ews-button ews-button-secondary" aria-label="Detail ujian {{ $ujian->judul }}">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7">Belum ada ujian. Pilih “Buat Ujian” untuk menambahkan ujian pertama.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-5">{{ $ujianList->links() }}</div>
    </section>
@endsection
