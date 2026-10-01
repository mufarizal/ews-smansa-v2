@extends('layouts.kurikulum')

@section('title', 'Tambah Mapel')

@section('kurikulum-content')
    <div class="ews-crud ews-crud-form">
        <a href="{{ route('kurikulum.mapels.index') }}" class="ews-back-link">← Kembali ke daftar</a>
        <h1 class="text-lg font-semibold mb-4">Tambah Mata Pelajaran</h1>

        <form action="{{ route('kurikulum.mapels.store') }}" method="POST">
            <div class="ews-fields">
            @csrf
            @if ($errors->any())
                <div class="ews-notice ews-notice-error" role="alert">
                    <strong>Data belum dapat disimpan.</strong>
                    <ul class="list-disc pl-5 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @php
                $mapel = $mapel ?? null;
            @endphp

            <div class="mb-3">
                <label for="field-nama" class="block text-sm mb-1">Nama Mata Pelajaran</label>
                <input id="field-nama" type="text" name="nama" aria-invalid="{{ $errors->has('nama') ? 'true' : 'false' }}" @error('nama') aria-describedby="error-nama" @enderror value="{{ old('nama', $mapel->nama ?? '') }}" placeholder="Contoh: Matematika"
                    class="w-full border rounded px-3 py-2 text-sm">
                @error('nama')
                    <p id="error-nama" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-3">
                <label for="field-kode" class="block text-sm mb-1">Kode (opsional)</label>
                <input id="field-kode" type="text" name="kode" aria-invalid="{{ $errors->has('kode') ? 'true' : 'false' }}" @error('kode') aria-describedby="error-kode" @enderror value="{{ old('kode', $mapel->kode ?? '') }}" placeholder="Contoh: MTK"
                    class="w-full border rounded px-3 py-2 text-sm">
                @error('kode')
                    <p id="error-kode" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            </div>
            <div class="ews-form-actions"><button type="submit" class="ews-button ews-button-primary">
                Simpan
            </button>
                <a href="{{ route('kurikulum.mapels.index') }}" class="ews-button ews-button-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
