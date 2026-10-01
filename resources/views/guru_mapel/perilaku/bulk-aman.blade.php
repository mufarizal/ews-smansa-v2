@extends('layouts.guru_mapel')

@section('title', 'Tandai Semua Aman')

@section('guru-mapel-content')
    <section class="ews-crud ews-crud-form">
        <a href="{{ route('guru_mapel.perilaku.index') }}" class="ews-back-link">← Kembali ke daftar</a>
        <h1>Tandai Semua Aman</h1>
        <p>Pencatatan perilaku positif untuk satu kelas.</p>
        @if ($errors->any())
            <div class="ews-notice ews-notice-error" role="alert">
                <strong>Periksa kembali isian berikut.</strong>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="mb-6 rounded-lg border border-orange-200 bg-orange-50 p-4 text-sm leading-7 text-orange-900">Siswa yang TIDAK izin/sakit/alpha pada tanggal ini akan otomatis dicatat perilaku positif default.</div>
        @if ($kelasList->isEmpty())
            <div class="ews-notice">Belum ada kelas yang dapat Anda akses.</div>
        @endif
        <form action="{{ route('guru_mapel.perilaku.bulk-aman.store') }}" method="POST" onsubmit="return confirm('Tandai semua siswa yang memenuhi ketentuan sebagai aman pada kelas dan tanggal ini?')">
            @csrf
            <div class="ews-fields">
                <div>
                    <label for="kelas_id" class="block">Kelas</label>
                    <select id="kelas_id" name="kelas_id" aria-invalid="{{ $errors->has('kelas_id') ? 'true' : 'false' }}" @error('kelas_id') aria-describedby="kelas_id-error" @enderror required>
                        <option value="">Pilih kelas</option>
                        @foreach ($kelasList as $kelas)
                            <option value="{{ $kelas->id }}" @selected(old('kelas_id', '') == $kelas->id)>{{ $kelas->nama }}</option>
                        @endforeach
                    </select>
                    @error('kelas_id')
                        <p id="kelas_id-error" class="mt-2 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="tanggal" class="block">Tanggal</label>
                    <input id="tanggal" name="tanggal" aria-invalid="{{ $errors->has('tanggal') ? 'true' : 'false' }}" @error('tanggal') aria-describedby="tanggal-error" @enderror type="date" value="{{ old('tanggal', now()->toDateString()) }}" required max="{{ now()->toDateString() }}">
                    @error('tanggal')
                        <p id="tanggal-error" class="mt-2 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="ews-form-actions">
                <button type="submit" class="ews-button ews-button-orange" @disabled($kelasList->isEmpty())>Tandai Semua Aman</button>
                <a href="{{ route('guru_mapel.perilaku.index') }}" class="ews-button ews-button-secondary">Batal</a>
            </div>
        </form>
    </section>
@endsection
