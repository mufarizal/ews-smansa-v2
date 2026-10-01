@extends('layouts.kurikulum')

@section('title', 'Import Guru')

@section('kurikulum-content')
    <div class="ews-crud ews-crud-form">
        <a href="{{ route('kurikulum.gurus.index') }}" class="ews-back-link">← Kembali ke daftar</a>
        <h1 class="text-lg font-semibold mb-4">Import Data Guru dari Excel</h1>

        <ol class="text-sm text-gray-600 list-decimal list-inside mb-4 space-y-1">
            <li>Download template di bawah.</li>
            <li>Isi data guru sesuai format (baca sheet "Petunjuk" di dalam file).</li>
            <li>Upload kembali file yang sudah diisi.</li>
        </ol>

        <a href="{{ route('kurikulum.gurus.import.template') }}"
            class="ews-button ews-button-secondary mb-4">
            ⬇ Download Template
        </a>

        @error('file')
            <p id="error-file" class="text-red-600 text-xs mb-2">{{ $message }}</p>
        @enderror

        <form action="{{ route('kurikulum.gurus.import.store') }}" method="POST" enctype="multipart/form-data">
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
            <div class="mb-3 ews-upload">
                <label for="import-file" class="block text-sm mb-2">File Excel</label>
                <input id="import-file" type="file" name="file" aria-invalid="{{ $errors->has('file') ? 'true' : 'false' }}" @error('file') aria-describedby="error-file" @enderror accept=".xlsx,.xls" class="text-sm" required>
            </div>
            </div>
            <div class="ews-form-actions"><button type="submit" class="ews-button ews-button-primary">
                Upload & Proses
            </button>
                <a href="{{ route('kurikulum.gurus.index') }}" class="ews-button ews-button-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
