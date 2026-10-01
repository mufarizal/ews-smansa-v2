@extends('layouts.kurikulum')

@section('title', 'Tambah Penugasan Guru BK')

@section('kurikulum-content')
    <div class="ews-crud ews-crud-form">
        <a href="{{ route('kurikulum.guru-bk-kelas.index') }}" class="ews-back-link">← Kembali ke daftar</a>
        <h1 class="text-lg font-semibold mb-4">Tambah Penugasan Guru BK</h1>

        @if ($kelasList->isEmpty())
            <p class="text-sm text-orange-600 mb-4">
                Semua kelas sudah punya guru BK, atau belum ada data kelas.
            </p>
        @endif

        <form action="{{ route('kurikulum.guru-bk-kelas.store') }}" method="POST">
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
                <label for="field-guru_id" class="block text-sm mb-1">Guru BK</label>
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

            <div class="mb-4">
                <label for="field-kelas_id" class="block text-sm mb-1">Kelas</label>
                <p class="text-xs text-gray-500 mb-1">Pilih satu atau beberapa kelas yang ditangani guru BK ini.</p>
                <select id="field-kelas_id" name="kelas_id[]" multiple size="10" aria-invalid="{{ $errors->has('kelas_id') ? 'true' : 'false' }}" @error('kelas_id') aria-describedby="error-kelas_id" @enderror class="w-full border rounded px-3 py-2 text-sm">
                    @foreach ($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ in_array($kelas->id, old('kelas_id', [])) ? 'selected' : '' }}>
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
                <a href="{{ route('kurikulum.guru-bk-kelas.index') }}" class="ews-button ews-button-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
