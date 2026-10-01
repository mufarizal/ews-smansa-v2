@extends('layouts.kurikulum')

@section('title', 'Edit Kehadiran Guru')

@section('kurikulum-content')
    <div class="ews-crud ews-crud-form">
        <a href="{{ route('kurikulum.kehadiran-guru.index') }}" class="ews-back-link">← Kembali ke daftar</a>
        <h1 class="text-lg font-semibold mb-1">Edit Kehadiran — {{ $kehadiranGuru->guru->nama }}</h1>
        <p class="text-xs text-gray-500 mb-4">
            {{ $kehadiranGuru->tanggal->format('d M Y') }} — Sesi {{ ucfirst($kehadiranGuru->sesi) }}
        </p>

        <form action="{{ route('kurikulum.kehadiran-guru.update', $kehadiranGuru) }}" method="POST">
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

            <div class="mb-3">
                <label for="field-status" class="block text-sm mb-1">Status</label>
                <select id="field-status" name="status" aria-invalid="{{ $errors->has('status') ? 'true' : 'false' }}" @error('status') aria-describedby="error-status" @enderror class="w-full border rounded px-3 py-2 text-sm">
                    @foreach (['hadir', 'izin', 'sakit', 'alpha'] as $status)
                        <option value="{{ $status }}"
                            {{ old('status', $kehadiranGuru->status) == $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
                @error('status')
                    <p id="error-status" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="field-keterangan" class="block text-sm mb-1">Keterangan (opsional)</label>
                <textarea id="field-keterangan" name="keterangan" aria-invalid="{{ $errors->has('keterangan') ? 'true' : 'false' }}" @error('keterangan') aria-describedby="error-keterangan" @enderror rows="2" class="w-full border rounded px-3 py-2 text-sm">{{ old('keterangan', $kehadiranGuru->keterangan) }}</textarea>
                @error('keterangan')
                    <p id="error-keterangan" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            </div>
            <div class="ews-form-actions"><button type="submit" class="ews-button ews-button-primary">
                Perbarui
            </button>
                <a href="{{ route('kurikulum.kehadiran-guru.index') }}" class="ews-button ews-button-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
