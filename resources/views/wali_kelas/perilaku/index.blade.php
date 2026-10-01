@extends('layouts.wali_kelas')

@section('title', 'Perilaku Siswa')

@section('wali-kelas-content')
    <section class="ews-crud">
        <header class="ews-crud-toolbar">
            <div>
                <h1>Perilaku Siswa</h1>
                <p class="mt-2 text-sm text-gray-600">Riwayat pencatatan perilaku siswa oleh Anda.</p>
            </div>
            <div class="ews-actions">
                <a href="{{ route('wali_kelas.perilaku.create') }}" class="ews-button ews-button-blue">+ Catat Individual</a>
                <a href="{{ route('wali_kelas.perilaku.bulk-aman.form') }}" class="ews-button ews-button-orange">Tandai Semua Aman</a>
                <a href="{{ route('wali_kelas.perilaku.bulk-bermasalah.form') }}" class="ews-button ews-button-orange">Catat Kelas Bermasalah</a>
            </div>
        </header>
        <p class="ews-table-hint md:hidden">Geser tabel ke samping untuk melihat seluruh kolom.</p>
        <div class="ews-table-scroll" role="region" aria-label="Daftar perilaku" tabindex="0">
            <table>
                <thead>
                    <tr>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Nama Siswa</th>
                        <th scope="col">Jenis Perilaku</th>
                        <th scope="col">Poin</th>
                        <th scope="col">Catatan</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayat as $item)
                        <tr>
                            <td class="whitespace-nowrap">{{ $item->tanggal->translatedFormat('d M Y') }}</td>
                            <td class="min-w-40 font-semibold">{{ $item->siswa->nama }}</td>
                            <td>
                                <p class="mb-2">{{ $item->perilaku->nama }}</p>
                                <span class="inline-flex rounded px-2.5 py-1 text-xs font-medium {{ $item->perilaku->jenis === 'positif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ ucfirst($item->perilaku->jenis) }}</span>
                            </td>
                            <td>{{ $item->perilaku->poin }}</td>
                            <td class="min-w-48 whitespace-pre-wrap break-words">{{ $item->catatan ?? '-' }}</td>
                            <td>
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('wali_kelas.perilaku.edit', $item) }}" class="ews-button ews-button-secondary">Edit</a>
                                    <form action="{{ route('wali_kelas.perilaku.destroy', $item) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus catatan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ews-button ews-button-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">Belum ada catatan perilaku siswa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-5">{{ $riwayat->links() }}</div>
    </section>
@endsection
