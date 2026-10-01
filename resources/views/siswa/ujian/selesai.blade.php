@extends('layouts.siswa')

@section('title', 'Hasil Ujian')

@section('siswa-content')
    <section class="ews-crud ews-crud-form" aria-labelledby="hasil-heading">
        <p class="ews-eyebrow">Hasil ujian harian</p>
        <h1 id="hasil-heading">{{ $ujian->judul }}</h1>
        @if ($hasil->status === 'selesai')
            <div class="rounded-lg border border-green-200 bg-green-50 p-6 text-center text-green-900 sm:p-10" role="status">
                <h2 class="text-xl font-semibold">Ujian selesai dinilai</h2>
                <p class="mt-5 text-sm">Nilai akhir</p>
                <p class="my-3 text-5xl font-bold tabular-nums">{{ $hasil->nilai ?? '-' }}</p>
                <p class="text-sm text-green-800">Terima kasih sudah menyelesaikan ujian.</p>
            </div>
        @elseif ($hasil->status === 'menunggu_penilaian')
            <div class="rounded-lg border border-orange-200 bg-orange-50 p-6 text-orange-900" role="status">
                <h2 class="text-lg font-semibold">Menunggu penilaian</h2>
                <p class="mt-3 leading-7">Ujian sudah dikumpulkan, menunggu penilaian guru untuk soal esai.</p>
            </div>
        @else
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-6 text-gray-700" role="status">Hasil ujian belum tersedia.</div>
        @endif
        <div class="ews-form-actions"><a href="{{ route('siswa.ujian.index') }}" class="ews-button ews-button-secondary">Kembali ke daftar ujian</a></div>
    </section>
@endsection
