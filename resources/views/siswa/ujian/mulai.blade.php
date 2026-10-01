@extends('layouts.siswa')

@section('title', 'Mulai Ujian')

@section('siswa-content')
    <section class="ews-crud ews-crud-form" aria-labelledby="mulai-heading">
        <a href="{{ route('siswa.ujian.index') }}" class="ews-back-link">← Kembali ke daftar ujian</a>
        <p class="ews-eyebrow">Persiapan ujian</p>
        <h1 id="mulai-heading">{{ $ujian->judul }}</h1>
        @if ($ujian->deskripsi)
            <div class="mb-6 whitespace-pre-wrap break-words text-sm leading-7 text-gray-600">{{ $ujian->deskripsi }}</div>
        @endif
        <dl class="mb-6 grid gap-5 rounded-lg border border-gray-200 bg-gray-50 p-5 sm:grid-cols-2">
            <div><dt class="text-xs text-gray-500">Mata pelajaran</dt><dd class="mt-1 font-semibold">{{ $ujian->mapel->nama }}</dd></div>
            <div><dt class="text-xs text-gray-500">Tanggal ujian</dt><dd class="mt-1 font-semibold">{{ $ujian->tanggal->translatedFormat('d M Y') }}</dd></div>
            <div><dt class="text-xs text-gray-500">Jumlah soal</dt><dd class="mt-1 font-semibold">{{ $jumlahSoal }} soal</dd></div>
            <div><dt class="text-xs text-gray-500">Durasi pengerjaan</dt><dd class="mt-1 font-semibold">{{ $ujian->durasi_menit }} menit</dd></div>
        </dl>
        <div class="rounded-lg border border-orange-200 bg-orange-50 p-5 text-sm leading-7 text-orange-900" role="note">
            <p class="font-semibold">Timer akan mulai berjalan begitu Anda klik Mulai, dan TIDAK bisa dijeda atau diulang dari awal.</p>
            <p class="mt-2">Simpan jawaban pada masing-masing soal. Saat waktu habis, ujian otomatis dikumpulkan dengan jawaban yang sudah tersimpan.</p>
        </div>
        <form action="{{ route('siswa.ujian.mulai', $ujian) }}" method="POST" class="ews-form-actions"
            onsubmit="return confirm('Mulai ujian sekarang? Timer langsung berjalan dan tidak bisa dijeda atau diulang dari awal.')">
            @csrf
            <button type="submit" class="ews-button ews-button-primary w-full sm:w-auto">Mulai Ujian</button>
            <a href="{{ route('siswa.ujian.index') }}" class="ews-button ews-button-secondary">Kembali</a>
        </form>
    </section>
@endsection
