@extends('layouts.kurikulum')

@section('title', 'Edit Siswa')

@section('kurikulum-content')
    <div class="ews-crud ews-crud-form">
        <a href="{{ route('kurikulum.siswas.index') }}" class="ews-back-link">← Kembali ke daftar</a>
        <h1 class="text-lg font-semibold mb-4">Edit Siswa</h1>

        <div class="mb-4 bg-gray-50 text-xs text-gray-600 px-3 py-2 rounded">
            Email login saat ini: <strong>{{ $siswa->user->email }}</strong><br>
            Mengubah NIS akan otomatis mengubah email login siswa ini.
        </div>

        <form action="{{ route('kurikulum.siswas.update', $siswa) }}" method="POST">
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
                $siswa = $siswa ?? null;
            @endphp

            <div class="mb-3">
                <label for="field-nis" class="block text-sm mb-1">NIS</label>
                <input id="field-nis" type="text" name="nis" aria-invalid="{{ $errors->has('nis') ? 'true' : 'false' }}" @error('nis') aria-describedby="error-nis" @enderror value="{{ old('nis', $siswa->nis ?? '') }}"
                    class="w-full border rounded px-3 py-2 text-sm">
                @error('nis')
                    <p id="error-nis" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-3">
                <label for="field-nama" class="block text-sm mb-1">Nama Lengkap</label>
                <input id="field-nama" type="text" name="nama" aria-invalid="{{ $errors->has('nama') ? 'true' : 'false' }}" @error('nama') aria-describedby="error-nama" @enderror value="{{ old('nama', $siswa->nama ?? '') }}"
                    class="w-full border rounded px-3 py-2 text-sm">
                @error('nama')
                    <p id="error-nama" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-3">
                <label for="field-jenis_kelamin" class="block text-sm mb-1">Jenis Kelamin</label>
                <select id="field-jenis_kelamin" name="jenis_kelamin" aria-invalid="{{ $errors->has('jenis_kelamin') ? 'true' : 'false' }}" @error('jenis_kelamin') aria-describedby="error-jenis_kelamin" @enderror class="w-full border rounded px-3 py-2 text-sm">
                    <option value="L" {{ old('jenis_kelamin', $siswa->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>
                        Laki-laki</option>
                    <option value="P" {{ old('jenis_kelamin', $siswa->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>
                        Perempuan</option>
                </select>
                @error('jenis_kelamin')
                    <p id="error-jenis_kelamin" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-3">
                <label for="field-kelas_id" class="block text-sm mb-1">Kelas (opsional)</label>
                <select id="field-kelas_id" name="kelas_id" aria-invalid="{{ $errors->has('kelas_id') ? 'true' : 'false' }}" @error('kelas_id') aria-describedby="error-kelas_id" @enderror class="w-full border rounded px-3 py-2 text-sm">
                    <option value="">- Belum ditentukan -</option>
                    @foreach ($kelas as $item)
                        <option value="{{ $item->id }}"
                            {{ old('kelas_id', $siswa->kelas_id ?? '') == $item->id ? 'selected' : '' }}>
                            {{ $item->nama }}
                        </option>
                    @endforeach
                </select>
                @error('kelas_id')
                    <p id="error-kelas_id" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-3">
                <label for="field-tanggal_lahir" class="block text-sm mb-1">Tanggal Lahir (opsional)</label>
                <input id="field-tanggal_lahir" type="date" name="tanggal_lahir" aria-invalid="{{ $errors->has('tanggal_lahir') ? 'true' : 'false' }}" @error('tanggal_lahir') aria-describedby="error-tanggal_lahir" @enderror
                    value="{{ old('tanggal_lahir', isset($siswa) && $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('Y-m-d') : '') }}"
                    class="w-full border rounded px-3 py-2 text-sm">
                @error('tanggal_lahir')
                    <p id="error-tanggal_lahir" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-3">
                <label for="field-alamat" class="block text-sm mb-1">Alamat (opsional)</label>
                <textarea id="field-alamat" name="alamat" aria-invalid="{{ $errors->has('alamat') ? 'true' : 'false' }}" @error('alamat') aria-describedby="error-alamat" @enderror rows="2" class="w-full border rounded px-3 py-2 text-sm">{{ old('alamat', $siswa->alamat ?? '') }}</textarea>
                @error('alamat')
                    <p id="error-alamat" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-3">
                <label for="field-nama_orang_tua" class="block text-sm mb-1">Nama Orang Tua (opsional)</label>
                <input id="field-nama_orang_tua" type="text" name="nama_orang_tua" aria-invalid="{{ $errors->has('nama_orang_tua') ? 'true' : 'false' }}" @error('nama_orang_tua') aria-describedby="error-nama_orang_tua" @enderror value="{{ old('nama_orang_tua', $siswa->nama_orang_tua ?? '') }}"
                    class="w-full border rounded px-3 py-2 text-sm">
                @error('nama_orang_tua')
                    <p id="error-nama_orang_tua" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-3">
                <label for="field-no_hp_orang_tua" class="block text-sm mb-1">No. HP Orang Tua (opsional)</label>
                <input id="field-no_hp_orang_tua" type="text" name="no_hp_orang_tua" aria-invalid="{{ $errors->has('no_hp_orang_tua') ? 'true' : 'false' }}" @error('no_hp_orang_tua') aria-describedby="error-no_hp_orang_tua" @enderror value="{{ old('no_hp_orang_tua', $siswa->no_hp_orang_tua ?? '') }}"
                    class="w-full border rounded px-3 py-2 text-sm">
                @error('no_hp_orang_tua')
                    <p id="error-no_hp_orang_tua" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            @unless ($siswa)
                <div class="mb-3 bg-blue-50 text-xs text-blue-700 px-3 py-2 rounded">
                    Password default akun ini: <strong>{{ config('school.default_password') }}</strong> — bisa diganti sendiri oleh
                    siswa setelah login.
                </div>
            @endunless

            </div>
            <div class="ews-form-actions"><button type="submit" class="ews-button ews-button-primary">
                Perbarui
            </button>
                <a href="{{ route('kurikulum.siswas.index') }}" class="ews-button ews-button-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
