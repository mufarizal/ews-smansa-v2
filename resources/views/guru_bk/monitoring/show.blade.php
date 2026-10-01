@extends('layouts.guru_bk')

@section('title', 'Monitoring Kelas')

@section('guru-bk-content')
    @php
        $kategoriStyles = [
            'aman' => 'bg-green-100 text-green-700',
            'perhatian' => 'bg-yellow-100 text-yellow-700',
            'binaan' => 'bg-red-100 text-red-700',
        ];
        $kategoriLabels = ['aman' => 'Aman', 'perhatian' => 'Perhatian', 'binaan' => 'Binaan'];
    @endphp
    <section class="ews-crud">
        <a class="ews-back-link" href="{{ route('guru_bk.monitoring.index') }}">← Kembali ke monitoring</a>
        <header class="ews-crud-toolbar">
            <div><h1>Monitoring {{ $kelas->nama }}</h1><p class="mt-2 text-sm text-gray-600">Siswa ditampilkan berdasarkan prioritas pendampingan.</p></div>
            <a class="ews-button ews-button-blue" href="{{ route('guru_bk.monitoring.export', $kelas) }}">Export Excel</a>
        </header>
        <p class="ews-table-hint md:hidden">Geser tabel ke samping untuk melihat seluruh kolom.</p>
        <div class="ews-table-scroll" role="region" aria-label="Monitoring siswa" tabindex="0">
            <table>
                <thead><tr><th scope="col">NIS</th><th scope="col">Nama</th><th scope="col">Kategori</th><th scope="col">Skor Akhir</th><th scope="col">Aksi</th></tr></thead>
                <tbody>
                    @forelse ($siswas as $siswa)
                        @php
                            $hasil = $siswa->ews_terkini;
                        @endphp
                        <tr>
                            <td>{{ $siswa->nis }}</td>
                            <td class="min-w-40 font-semibold">{{ $siswa->nama }}</td>
                            <td>
                                <span class="inline-flex rounded px-2.5 py-1 text-xs font-medium {{ $kategoriStyles[$hasil?->kategori] ?? 'bg-gray-100 text-gray-700' }}">{{ $kategoriLabels[$hasil?->kategori] ?? 'Belum ada data' }}</span>
                                @if ($hasil?->data_tidak_lengkap)
                                    <span class="mt-2 inline-flex rounded bg-yellow-100 px-2.5 py-1 text-xs font-medium text-yellow-700">⚠ Data Tidak Lengkap</span>
                                @endif
                            </td>
                            <td class="tabular-nums">{{ $hasil?->skor_akhir === null ? '-' : number_format((float) $hasil->skor_akhir, 2, ',', '.') }}</td>
                            <td><a class="ews-button ews-button-secondary" href="{{ route('guru_bk.monitoring.siswa', [$kelas, $siswa]) }}" aria-label="Detail {{ $siswa->nama }}">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5">Belum ada siswa untuk ditampilkan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
