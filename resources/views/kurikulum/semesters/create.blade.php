@extends('layouts.kurikulum')

@section('title', 'Tambah Semester')

@section('kurikulum-content')
    <div class="ews-crud ews-crud-form">
        <a href="{{ route('kurikulum.semesters.index') }}" class="ews-back-link">← Kembali ke daftar</a>
        <h1 class="text-lg font-semibold mb-4">Tambah Semester</h1>

        <form action="{{ route('kurikulum.semesters.store') }}" method="POST">
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
                $semester = $semester ?? null;
            @endphp

            <div class="mb-3">
                <label for="field-nama" class="block text-sm mb-1">Nama Semester</label>
                <input id="field-nama" type="text" name="nama" aria-invalid="{{ $errors->has('nama') ? 'true' : 'false' }}" @error('nama') aria-describedby="error-nama" @enderror value="{{ old('nama', $semester->nama ?? '') }}"
                    placeholder="Contoh: Ganjil 2025/2026" class="w-full border rounded px-3 py-2 text-sm">
                @error('nama')
                    <p id="error-nama" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-3">
                <label for="field-jenis" class="block text-sm mb-1">Jenis</label>
                <select id="field-jenis" name="jenis" aria-invalid="{{ $errors->has('jenis') ? 'true' : 'false' }}" @error('jenis') aria-describedby="error-jenis" @enderror class="w-full border rounded px-3 py-2 text-sm">
                    <option value="ganjil" {{ old('jenis', $semester->jenis ?? '') == 'ganjil' ? 'selected' : '' }}>Ganjil</option>
                    <option value="genap" {{ old('jenis', $semester->jenis ?? '') == 'genap' ? 'selected' : '' }}>Genap</option>
                </select>
                @error('jenis')
                    <p id="error-jenis" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-3">
                <label for="field-tahun_ajaran" class="block text-sm mb-1">Tahun Ajaran</label>
                <input id="field-tahun_ajaran" type="text" name="tahun_ajaran" aria-invalid="{{ $errors->has('tahun_ajaran') ? 'true' : 'false' }}" @error('tahun_ajaran') aria-describedby="error-tahun_ajaran" @enderror value="{{ old('tahun_ajaran', $semester->tahun_ajaran ?? '') }}"
                    placeholder="Contoh: 2025/2026" class="w-full border rounded px-3 py-2 text-sm">
                @error('tahun_ajaran')
                    <p id="error-tahun_ajaran" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="field-tanggal_mulai" class="block text-sm mb-1">Tanggal Mulai</label>
                    <input id="field-tanggal_mulai" type="date" name="tanggal_mulai" aria-invalid="{{ $errors->has('tanggal_mulai') ? 'true' : 'false' }}" @error('tanggal_mulai') aria-describedby="error-tanggal_mulai" @enderror
                        value="{{ old('tanggal_mulai', isset($semester) ? $semester->tanggal_mulai->format('Y-m-d') : '') }}"
                        class="w-full border rounded px-3 py-2 text-sm">
                    @error('tanggal_mulai')
                        <p id="error-tanggal_mulai" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="field-tanggal_selesai" class="block text-sm mb-1">Tanggal Selesai</label>
                    <input id="field-tanggal_selesai" type="date" name="tanggal_selesai" aria-invalid="{{ $errors->has('tanggal_selesai') ? 'true' : 'false' }}" @error('tanggal_selesai') aria-describedby="error-tanggal_selesai" @enderror
                        value="{{ old('tanggal_selesai', isset($semester) ? $semester->tanggal_selesai->format('Y-m-d') : '') }}"
                        class="w-full border rounded px-3 py-2 text-sm">
                    @error('tanggal_selesai')
                        <p id="error-tanggal_selesai" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-3 flex items-center">
                    <input type="checkbox" name="is_aktif" id="is_aktif" value="1"
                        {{ old('is_aktif', $semester->is_aktif ?? false) ? 'checked' : '' }} class="mr-2">
                    <label for="is_aktif" class="text-sm">
                        Jadikan semester ini aktif
                        <span class="text-gray-400 block text-xs">Semester lain yang sedang aktif akan otomatis
                            dinonaktifkan.</span>
                    </label>
                </div>
            </div>

            </div>
            <div class="ews-form-actions"><button type="submit" class="ews-button ews-button-primary">
                Simpan
            </button>
                <a href="{{ route('kurikulum.semesters.index') }}" class="ews-button ews-button-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
