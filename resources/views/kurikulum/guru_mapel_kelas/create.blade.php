@extends('layouts.kurikulum')

@section('title', 'Tambah Penugasan Mengajar')

@section('kurikulum-content')
    <div class="ews-crud ews-crud-form">
        <a href="{{ route('kurikulum.guru-mapel-kelas.index') }}" class="ews-back-link">← Kembali ke daftar</a>
        <h1 class="text-lg font-semibold mb-4">Tambah Penugasan Mengajar</h1>

        @if ($gurus->isEmpty() || $mapels->isEmpty() || $kelasList->isEmpty())
            <p class="text-sm text-orange-600 mb-4">
                Pastikan data Guru, Mapel, dan Kelas sudah ada sebelum membuat penugasan.
            </p>
        @endif

        <form action="{{ route('kurikulum.guru-mapel-kelas.store') }}" method="POST">
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

            <div class="mb-3">
                <label for="field-guru_id" class="block text-sm mb-1">Guru</label>
                <select id="field-guru_id" name="guru_id" aria-invalid="{{ $errors->has('guru_id') ? 'true' : 'false' }}" @error('guru_id') aria-describedby="error-guru_id" @enderror class="w-full border rounded px-3 py-2 text-sm">
                    <option value="">- Pilih Guru -</option>
                    @foreach ($gurus as $guru)
                        <option value="{{ $guru->id }}" {{ old('guru_id') == $guru->id ? 'selected' : '' }}>
                            {{ $guru->nama }}
                        </option>
                    @endforeach
                </select>
                @error('guru_id')
                    <p id="error-guru_id" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-3">
                <label for="field-mapel_id" class="block text-sm mb-1">Mata Pelajaran</label>
                <select id="field-mapel_id" name="mapel_id" aria-invalid="{{ $errors->has('mapel_id') ? 'true' : 'false' }}" @error('mapel_id') aria-describedby="error-mapel_id" @enderror class="w-full border rounded px-3 py-2 text-sm">
                    <option value="">- Pilih Mapel -</option>
                    @foreach ($mapels as $mapel)
                        <option value="{{ $mapel->id }}" {{ old('mapel_id') == $mapel->id ? 'selected' : '' }}>
                            {{ $mapel->nama }}
                        </option>
                    @endforeach
                </select>
                @error('mapel_id')
                    <p id="error-mapel_id" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="field-kelas_id" class="block text-sm mb-1">Kelas</label>
                <select id="field-kelas_id" name="kelas_id" aria-invalid="{{ $errors->has('kelas_id') ? 'true' : 'false' }}" @error('kelas_id') aria-describedby="error-kelas_id" @enderror class="w-full border rounded px-3 py-2 text-sm">
                    <option value="">- Pilih Kelas -</option>
                    @foreach ($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ old('kelas_id') == $kelas->id ? 'selected' : '' }}>
                            {{ $kelas->nama }}
                        </option>
                    @endforeach
                </select>
                @error('kelas_id')
                    <p id="error-kelas_id" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            </div>
            <div class="ews-form-actions"><button type="submit" class="ews-button ews-button-primary">
                Simpan
            </button>
                <a href="{{ route('kurikulum.guru-mapel-kelas.index') }}" class="ews-button ews-button-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
