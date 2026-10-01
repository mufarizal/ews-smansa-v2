@extends('layouts.guru_bk')

@section('title', 'Master Perilaku')

@section('guru-bk-content')
    <section class="ews-crud">
        <header class="ews-crud-toolbar">
            <div>
                <h1>Master Perilaku</h1>
                <p class="mt-2 text-sm text-gray-600">Kelola jenis perilaku dan poin yang digunakan untuk pencatatan siswa.</p>
            </div>
            <a href="{{ route('guru_bk.perilaku.create') }}" class="ews-button ews-button-blue">+ Tambah Jenis Perilaku</a>
        </header>
        <p class="ews-table-hint md:hidden">Geser tabel ke samping untuk melihat seluruh kolom.</p>
        <div class="ews-table-scroll" role="region" aria-label="Daftar perilaku" tabindex="0">
            <table>
                <thead>
                    <tr>
                        <th scope="col">Nama</th>
                        <th scope="col">Jenis</th>
                        <th scope="col">Poin</th>
                        <th scope="col">Default Kelas Aman</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($perilakus as $perilaku)
                        <tr>
                            <td class="min-w-48">
                                <strong>{{ $perilaku->nama }}</strong>
                                @if ($perilaku->keterangan)
                                    <p class="mt-1 whitespace-pre-wrap break-words text-xs text-gray-600">{{ $perilaku->keterangan }}</p>
                                @endif
                            </td>
                            <td>
                                <span class="inline-flex rounded px-2.5 py-1 text-xs font-medium {{ $perilaku->jenis === 'positif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ ucfirst($perilaku->jenis) }}</span>
                            </td>
                            <td class="tabular-nums">{{ $perilaku->poin }}</td>
                            <td>
                                @if ($perilaku->is_default_aman)
                                    <span class="font-semibold text-green-800">★ Default</span>
                                @elseif ($perilaku->jenis === 'positif')
                                    <form action="{{ route('guru_bk.perilaku.jadikan-default-aman', $perilaku) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="ews-button ews-button-secondary">Jadikan Default</button>
                                    </form>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('guru_bk.perilaku.edit', $perilaku) }}" class="ews-button ews-button-secondary">Edit</a>
                                    <form action="{{ route('guru_bk.perilaku.destroy', $perilaku) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus catatan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ews-button ews-button-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">Belum ada jenis perilaku. Tambahkan jenis perilaku untuk memulai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-5">{{ $perilakus->links() }}</div>
    </section>
@endsection
