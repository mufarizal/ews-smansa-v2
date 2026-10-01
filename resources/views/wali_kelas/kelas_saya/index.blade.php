@extends('layouts.wali_kelas')

@section('title', 'Kelas Saya')

@section('wali-kelas-content')
    @php
        $kategoriStyles = [
            'aman' => 'bg-green-100 text-green-700',
            'perhatian' => 'bg-yellow-100 text-yellow-700',
            'binaan' => 'bg-red-100 text-red-700',
        ];
        $kategoriLabels = ['aman' => 'Aman', 'perhatian' => 'Perhatian', 'binaan' => 'Binaan'];
    @endphp
    <section class="ews-crud">
        <header class="ews-crud-toolbar">
            <div><h1>Kelas Saya</h1><p class="mt-2 text-sm text-gray-600">{{ $kelasTerpilih ? 'Monitoring siswa kelas ' . $kelasTerpilih->nama : 'Pantau perkembangan siswa pada kelas yang Anda dampingi.' }}</p></div>
            @if ($kelasTerpilih)
                <a class="ews-button ews-button-blue" href="{{ route('wali_kelas.kelas-saya.export', $kelasTerpilih) }}">Export Excel</a>
            @endif
        </header>
        @if ($kelasList->count() > 1)
            <form method="GET" action="{{ route('wali_kelas.kelas-saya.index') }}" class="ews-filter mb-6">
                <div>
                    <label for="kelas_id">Pilih kelas</label>
                    <select id="kelas_id" name="kelas_id" onchange="this.form.submit()">
                        <option value="">Pilih kelas</option>
                        @foreach ($kelasList as $kelas)
                            <option value="{{ $kelas->id }}" @selected($kelasTerpilih?->id == $kelas->id)>{{ $kelas->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <noscript><button class="ews-button ews-button-blue" type="submit">Tampilkan</button></noscript>
            </form>
        @endif
        @if ($kelasTerpilih)
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
                            <td><a class="ews-button ews-button-secondary" href="{{ route('wali_kelas.kelas-saya.siswa', $siswa) }}" aria-label="Detail {{ $siswa->nama }}">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5">Belum ada siswa untuk ditampilkan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @else
            <p class="text-sm text-gray-600">{{ $kelasList->isEmpty() ? 'Belum ada kelas yang ditugaskan kepada Anda.' : 'Pilih kelas yang tersedia untuk melihat siswa.' }}</p>
        @endif
    </section>
@endsection
