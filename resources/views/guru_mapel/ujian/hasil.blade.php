@extends('layouts.guru_mapel')

@section('title', 'Hasil Ujian')

@section('guru-mapel-content')
    @php
        $statusLabels = [
            'belum_mulai' => 'Belum Mulai',
            'sedang_mengerjakan' => 'Sedang Dikerjakan',
            'menunggu_penilaian' => 'Menunggu penilaian',
            'selesai' => 'Selesai',
        ];
        $statusColors = [
            'belum_mulai' => 'bg-gray-100 text-gray-700',
            'sedang_mengerjakan' => 'bg-yellow-100 text-yellow-800',
            'menunggu_penilaian' => 'bg-orange-100 text-orange-800',
            'selesai' => 'bg-green-100 text-green-800',
        ];
    @endphp

    <div class="ews-crud">
        <a href="{{ route('guru_mapel.ujian.show', $ujian) }}" class="ews-back-link">← Kembali ke detail ujian</a>

        <div class="ews-crud-toolbar">
            <div class="min-w-0">
                <h1>Hasil Ujian</h1>
                <p class="mt-2 break-words text-sm text-gray-600">{{ $ujian->judul }}</p>
            </div>
            <span class="rounded-full bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-700">{{ $siswas->count() }} siswa</span>
        </div>

        <p class="mb-4 text-sm leading-6 text-gray-600">Pantau pengerjaan ujian dan berikan nilai setelah siswa mengumpulkan jawaban. Nilai akhir ditampilkan saat penilaian selesai.</p>

        @if ($siswas->isEmpty())
            <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 px-5 py-10 text-center">
                <h2 class="text-base font-semibold text-gray-800">Belum ada siswa di kelas ini</h2>
                <p class="mt-2 text-sm text-gray-600">Daftar pengerjaan akan tampil setelah data siswa tersedia.</p>
            </div>
        @else
            <p class="ews-table-hint md:hidden" id="progress-table-hint">Geser tabel ke samping untuk melihat status, nilai, dan tombol penilaian.</p>
            <div class="ews-table-scroll" role="region" aria-label="Hasil ujian siswa" tabindex="0">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">NIS</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Status pengerjaan</th>
                            <th scope="col">Waktu Mulai</th>
                            <th scope="col">Nilai</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($siswas as $siswa)
                            @php
                                $hasil = $hasilList->get($siswa->id);
                                $status = $hasil?->status ?? 'belum_mulai';
                            @endphp
                            <tr>
                                <td class="whitespace-nowrap tabular-nums">{{ $siswa->nis }}</td>
                                <td class="min-w-44 font-semibold text-gray-800">{{ $siswa->nama }}</td>
                                <td>
                                    <span class="inline-flex whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold {{ $statusColors[$status] ?? $statusColors['belum_mulai'] }}">
                                        {{ $statusLabels[$status] ?? 'Belum Mulai' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap text-sm tabular-nums">{{ $hasil?->waktu_mulai?->translatedFormat('d M Y, H:i') ?? '-' }}</td>
                                <td class="font-semibold tabular-nums text-gray-800">{{ $status === 'selesai' ? ($hasil?->nilai ?? '-') : '-' }}</td>
                                <td>
                                    @if (in_array($status, ['menunggu_penilaian', 'selesai'], true))
                                        <a href="{{ route('guru_mapel.ujian.nilai.form', [$ujian, $siswa]) }}"
                                            class="ews-button ews-button-secondary" aria-label="Nilai ujian {{ $siswa->nama }}">Nilai</a>
                                    @else
                                        <span class="text-xs text-gray-500">Belum dikumpulkan</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
