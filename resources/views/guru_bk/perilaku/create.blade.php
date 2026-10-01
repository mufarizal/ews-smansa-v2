@extends('layouts.guru_bk')

@section('title', 'Tambah Jenis Perilaku')

@section('guru-bk-content')
    <section class="ews-crud ews-crud-form">
        <a href="{{ route('guru_bk.perilaku.index') }}" class="ews-back-link">← Kembali ke daftar</a>
        <h1>Tambah Jenis Perilaku</h1>
        <p>Tentukan jenis perilaku dan jumlah poinnya.</p>
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
        <form action="{{ route('guru_bk.perilaku.store') }}" method="POST">
            @csrf
            <div class="ews-fields">
                <div>
                    <label for="nama" class="block">Nama perilaku</label>
                    <input id="nama" name="nama" aria-invalid="{{ $errors->has('nama') ? 'true' : 'false' }}" @error('nama') aria-describedby="nama-error" @enderror type="text" value="{{ old('nama', '') }}" required maxlength="150">
                    @error('nama')
                        <p id="nama-error" class="mt-2 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="jenis" class="block">Jenis</label>
                    <select id="jenis" name="jenis" aria-invalid="{{ $errors->has('jenis') ? 'true' : 'false' }}" @error('jenis') aria-describedby="jenis-error" @enderror required>
                        <option value="positif" @selected(old('jenis', 'positif') === 'positif')>Positif</option>
                        <option value="negatif" @selected(old('jenis', 'positif') === 'negatif')>Negatif</option>
                    </select>
                    @error('jenis')
                        <p id="jenis-error" class="mt-2 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="poin" class="block">Poin</label>
                    <input id="poin" name="poin" aria-invalid="{{ $errors->has('poin') ? 'true' : 'false' }}" @error('poin') aria-describedby="poin-error" @enderror type="number" value="{{ old('poin', '') }}" required min="1" step="1">
                    @error('poin')
                        <p id="poin-error" class="mt-2 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="keterangan" class="block">Keterangan (opsional)</label>
                    <textarea id="keterangan" name="keterangan" aria-invalid="{{ $errors->has('keterangan') ? 'true' : 'false' }}" @error('keterangan') aria-describedby="keterangan-error" @enderror rows="4">{{ old('keterangan', '') }}</textarea>
                    @error('keterangan')
                        <p id="keterangan-error" class="mt-2 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="ews-form-actions">
                <button type="submit" class="ews-button ews-button-blue">Simpan</button>
                <a href="{{ route('guru_bk.perilaku.index') }}" class="ews-button ews-button-secondary">Batal</a>
            </div>
        </form>
    </section>
@endsection
