@extends('layouts.kurikulum')

@section('title', 'Edit Guru')

@section('kurikulum-content')
    <div class="ews-crud ews-crud-form">
        <a href="{{ route('kurikulum.gurus.index') }}" class="ews-back-link">← Kembali ke daftar</a>
        <h1 class="text-lg font-semibold mb-4">Edit Guru</h1>

        <div class="mb-4 bg-gray-50 text-xs text-gray-600 px-3 py-2 rounded">
            Email login saat ini: <strong>{{ $guru->user->email }}</strong><br>
            Mengubah NIP akan otomatis mengubah email login guru ini.
        </div>

        <form action="{{ route('kurikulum.gurus.update', $guru) }}" method="POST">
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
                $guru = $guru ?? null;
            @endphp

            <div class="mb-3">
                <label for="field-nip" class="block text-sm mb-1">NIP</label>
                <input id="field-nip" type="text" name="nip" aria-invalid="{{ $errors->has('nip') ? 'true' : 'false' }}" @error('nip') aria-describedby="error-nip" @enderror value="{{ old('nip', $guru->nip ?? '') }}"
                    class="w-full border rounded px-3 py-2 text-sm">
                @error('nip')
                    <p id="error-nip" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-3">
                <label for="field-nama" class="block text-sm mb-1">Nama Lengkap</label>
                <input id="field-nama" type="text" name="nama" aria-invalid="{{ $errors->has('nama') ? 'true' : 'false' }}" @error('nama') aria-describedby="error-nama" @enderror value="{{ old('nama', $guru->nama ?? '') }}"
                    class="w-full border rounded px-3 py-2 text-sm">
                @error('nama')
                    <p id="error-nama" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-3">
                <label for="field-jenis_kelamin" class="block text-sm mb-1">Jenis Kelamin</label>
                <select id="field-jenis_kelamin" name="jenis_kelamin" aria-invalid="{{ $errors->has('jenis_kelamin') ? 'true' : 'false' }}" @error('jenis_kelamin') aria-describedby="error-jenis_kelamin" @enderror class="w-full border rounded px-3 py-2 text-sm">
                    <option value="L" {{ old('jenis_kelamin', $guru->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>Laki-laki
                    </option>
                    <option value="P" {{ old('jenis_kelamin', $guru->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>Perempuan
                    </option>
                </select>
                @error('jenis_kelamin')
                    <p id="error-jenis_kelamin" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-3">
                <label for="field-no_hp" class="block text-sm mb-1">No. HP (opsional)</label>
                <input id="field-no_hp" type="text" name="no_hp" aria-invalid="{{ $errors->has('no_hp') ? 'true' : 'false' }}" @error('no_hp') aria-describedby="error-no_hp" @enderror value="{{ old('no_hp', $guru->no_hp ?? '') }}"
                    class="w-full border rounded px-3 py-2 text-sm">
                @error('no_hp')
                    <p id="error-no_hp" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-3">
                <label for="field-alamat" class="block text-sm mb-1">Alamat (opsional)</label>
                <textarea id="field-alamat" name="alamat" aria-invalid="{{ $errors->has('alamat') ? 'true' : 'false' }}" @error('alamat') aria-describedby="error-alamat" @enderror rows="2" class="w-full border rounded px-3 py-2 text-sm">{{ old('alamat', $guru->alamat ?? '') }}</textarea>
                @error('alamat')
                    <p id="error-alamat" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            @unless ($guru)
                <div class="mb-3 bg-blue-50 text-xs text-blue-700 px-3 py-2 rounded">
                    Password default akun ini: <strong>{{ config('school.default_password') }}</strong> — bisa diganti sendiri oleh
                    guru setelah login.
                </div>
            @endunless

            </div>
            <div class="ews-form-actions"><button type="submit" class="ews-button ews-button-primary">
                Perbarui
            </button>
                <a href="{{ route('kurikulum.gurus.index') }}" class="ews-button ews-button-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
