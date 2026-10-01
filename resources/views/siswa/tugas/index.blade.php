@extends('layouts.siswa')

@section('title', 'Tugas Saya')

@section('siswa-content')
    @php
        $statusLabels = [
            'belum_dikerjakan' => 'Belum dikerjakan',
            'sedang_dikerjakan' => 'Sedang dikerjakan',
            'menunggu_penilaian' => 'Menunggu penilaian',
            'selesai' => 'Selesai',
        ];
        $statusColors = [
            'belum_dikerjakan' => 'border-gray-200 bg-gray-100 text-gray-700',
            'sedang_dikerjakan' => 'border-yellow-200 bg-yellow-50 text-yellow-800',
            'menunggu_penilaian' => 'border-orange-200 bg-orange-50 text-orange-800',
            'selesai' => 'border-green-200 bg-green-50 text-green-800',
        ];
    @endphp

    <div class="ews-crud">
        <div class="ews-crud-toolbar">
            <div class="min-w-0">
                <p class="ews-eyebrow">Pembelajaran</p>
                <h1>Tugas Saya</h1>
                <p class="mt-2 text-sm text-gray-600">Lihat tugas dari guru, simpan jawaban, dan pantau hasil penilaian.</p>
            </div>
            <span class="rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-xs font-medium text-gray-600">{{ $tugasList->count() }} tugas</span>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            @forelse ($tugasList as $tugas)
                @php
                    $nilaiTugas = $tugas->nilai_tugas;
                    $status = $nilaiTugas?->status ?? 'belum_dikerjakan';
                    $sudahDikumpulkan = in_array($status, ['menunggu_penilaian', 'selesai'], true);
                @endphp
                <article class="flex min-w-0 flex-col rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                        <p class="text-xs font-semibold text-[#426341]">{{ $tugas->mapel?->nama ?? 'Mata pelajaran' }}</p>
                        <span class="inline-flex rounded-md border px-2.5 py-1 text-xs font-medium {{ $statusColors[$status] ?? $statusColors['belum_dikerjakan'] }}">
                            {{ $statusLabels[$status] ?? 'Belum dikerjakan' }}
                        </span>
                    </div>
                    <h2 class="break-words text-lg font-semibold leading-snug text-gray-900">{{ $tugas->judul }}</h2>
                    <dl class="mt-5 grid grid-cols-2 gap-4 border-t border-gray-100 pt-4 text-sm">
                        <div>
                            <dt class="text-xs text-gray-500">Batas pengumpulan</dt>
                            <dd class="mt-1 font-medium text-gray-800">
                                <time datetime="{{ $tugas->tanggal_selesai->toIso8601String() }}">{{ $tugas->tanggal_selesai->translatedFormat('d M Y') }}</time>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">Nilai akhir</dt>
                            <dd class="mt-1 font-semibold {{ $status === 'selesai' ? 'text-green-800' : 'text-gray-500' }}">
                                {{ $status === 'selesai' ? ($nilaiTugas?->nilai ?? '-') : '-' }}
                            </dd>
                        </div>
                    </dl>
                    <div class="mt-auto pt-5">
                        <a href="{{ route('siswa.tugas.show', $tugas) }}"
                            class="ews-button {{ $sudahDikumpulkan ? 'ews-button-secondary' : 'ews-button-primary' }} w-full sm:w-auto"
                            aria-label="{{ $sudahDikumpulkan ? 'Lihat' : 'Kerjakan' }} tugas {{ $tugas->judul }}">
                            {{ $sudahDikumpulkan ? 'Lihat' : 'Kerjakan' }}
                        </a>
                    </div>
                </article>
            @empty
                <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 px-5 py-12 text-center lg:col-span-2">
                    <h2 class="font-semibold text-gray-800">Belum ada tugas</h2>
                    <p class="mt-2 text-sm text-gray-600">Tugas yang dipublikasikan guru untuk kelasmu akan muncul di sini.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
