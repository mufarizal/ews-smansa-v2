@extends('layouts.kurikulum')

@section('title', 'Edit Kelas')

@section('kurikulum-content')
    <div class="ews-crud ews-crud-form">
        <a href="{{ route('kurikulum.kelas.index') }}" class="ews-back-link">← Kembali ke daftar</a>
        <h1 class="text-lg font-semibold mb-4">Edit Kelas</h1>

        <form action="{{ route('kurikulum.kelas.update', $kelas) }}" method="POST">
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
            @method('PUT')
            @php
                $kelas = $kelas ?? null;
            @endphp

            <div class="mb-3">
                <label for="field-nama" class="block text-sm mb-1">Nama Kelas</label>
                <input id="field-nama" type="text" name="nama" aria-invalid="{{ $errors->has('nama') ? 'true' : 'false' }}" @error('nama') aria-describedby="error-nama" @enderror value="{{ old('nama', $kelas->nama ?? '') }}" placeholder="Contoh: X-A"
                    class="w-full border rounded px-3 py-2 text-sm">
                @error('nama')
                    <p id="error-nama" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-3">
                <label for="field-tingkat" class="block text-sm mb-1">Tingkat</label>
                <input id="field-tingkat" type="text" name="tingkat" aria-invalid="{{ $errors->has('tingkat') ? 'true' : 'false' }}" @error('tingkat') aria-describedby="error-tingkat" @enderror value="{{ old('tingkat', $kelas->tingkat ?? '') }}"
                    placeholder="Contoh: X, XI, XII" class="w-full border rounded px-3 py-2 text-sm">
                @error('tingkat')
                    <p id="error-tingkat" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-3">
                <label for="field-wali_kelas_id" class="block text-sm mb-1">Wali Kelas (opsional)</label>
                <select id="field-wali_kelas_id" name="wali_kelas_id" aria-invalid="{{ $errors->has('wali_kelas_id') ? 'true' : 'false' }}" @error('wali_kelas_id') aria-describedby="error-wali_kelas_id" @enderror class="w-full border rounded px-3 py-2 text-sm">
                    <option value="">- Belum ditentukan -</option>
                    @foreach ($gurus as $guru)
                        <option value="{{ $guru->id }}"
                            {{ old('wali_kelas_id', $kelas->wali_kelas_id ?? '') == $guru->id ? 'selected' : '' }}>
                            {{ $guru->nama }}
                        </option>
                    @endforeach
                </select>
                @error('wali_kelas_id')
                    <p id="error-wali_kelas_id" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
                @if ($gurus->isEmpty())
                    <p class="text-xs text-gray-400 mt-1">Belum ada data guru — tambahkan guru dulu untuk bisa pilih wali kelas.</p>
                @endif
            </div>

            </div>
            <div class="ews-form-actions"><button type="submit" class="ews-button ews-button-primary">
                Perbarui
            </button>
                <a href="{{ route('kurikulum.kelas.index') }}" class="ews-button ews-button-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
