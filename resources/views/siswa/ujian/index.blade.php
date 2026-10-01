@extends('layouts.siswa')

@section('title', 'Ujian Harian')

@section('siswa-content')
    @php
        $statuses = [
            'sedang_mengerjakan' => ['Sedang Dikerjakan', 'bg-yellow-100 text-yellow-800', 'Lanjutkan'],
            'menunggu_penilaian' => ['Menunggu Penilaian', 'bg-orange-100 text-orange-800', 'Lihat Hasil'],
            'selesai' => ['Selesai', 'bg-green-100 text-green-800', 'Lihat Hasil'],
        ];
    @endphp
    <section class="ews-crud" aria-labelledby="ujian-heading">
        <header class="ews-crud-toolbar">
            <div>
                <h1 id="ujian-heading">Ujian Harian</h1>
                <p class="mt-2 text-sm text-gray-600">Lihat jadwal ujian, lanjutkan pengerjaan, dan periksa hasilnya.</p>
            </div>
        </header>
        <div class="grid gap-4 lg:grid-cols-2">
            @forelse ($ujianList as $ujian)
                @php
                    $status = $ujian->hasil?->status;
                    [$label, $color, $action] = $statuses[$status] ?? ['Belum Dimulai', 'bg-gray-100 text-gray-700', 'Mulai'];
                @endphp
                <article class="flex min-w-0 flex-col rounded-lg border border-gray-200 p-5 shadow-sm">
                    <div><span class="inline-flex rounded px-2.5 py-1 text-xs font-medium {{ $color }}">{{ $label }}</span></div>
                    <h2 class="mt-4 break-words text-lg font-semibold">{{ $ujian->judul }}</h2>
                    <p class="mt-1 text-sm text-gray-600">{{ $ujian->mapel->nama }}</p>
                    <dl class="my-5 grid grid-cols-2 gap-4 text-sm">
                        <div><dt class="text-xs text-gray-500">Tanggal</dt><dd class="mt-1 font-medium">{{ $ujian->tanggal->translatedFormat('d M Y') }}</dd></div>
                        <div><dt class="text-xs text-gray-500">Durasi</dt><dd class="mt-1 font-medium">{{ $ujian->durasi_menit }} menit</dd></div>
                    </dl>
                    <div class="mt-auto flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 pt-4">
                        <p class="text-sm text-gray-600">Nilai: <strong>{{ $status === 'selesai' ? ($ujian->hasil->nilai ?? '-') : '-' }}</strong></p>
                        <a href="{{ route('siswa.ujian.show', $ujian) }}" class="ews-button ews-button-secondary" aria-label="{{ $action }} ujian {{ $ujian->judul }}">{{ $action }}</a>
                    </div>
                </article>
            @empty
                <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 p-8 text-center lg:col-span-2">
                    <h2 class="font-semibold">Belum ada ujian</h2>
                    <p class="mt-2 text-sm text-gray-600">Ujian akan tampil di sini setelah dipublikasikan oleh guru.</p>
                </div>
            @endforelse
        </div>
    </section>
@endsection
