@extends('layouts.kurikulum')

@section('title', 'Tambah Jadwal')

@section('kurikulum-content')
    <div class="ews-crud ews-crud-form">
        <a href="{{ route('kurikulum.jadwals.index', ['kelas_id' => $kelas->id]) }}" class="ews-back-link">← Kembali ke daftar</a>
        <h1 class="text-lg font-semibold mb-1">Tambah Jadwal — Kelas {{ $kelas->nama }}</h1>

        @if (!$semesterAktif)
            <p class="text-sm text-red-600 mt-2">Tidak ada semester aktif. Aktifkan semester dulu di menu Semester.</p>
        @elseif ($penugasans->isEmpty())
            <p class="text-sm text-orange-600 mt-2">
                Kelas ini belum punya penugasan guru mengajar. Tambahkan dulu di menu "Penugasan Mengajar".
            </p>
        @else
            <p class="text-xs text-gray-500 mb-4">Semester aktif: {{ $semesterAktif->nama }}</p>

            <form action="{{ route('kurikulum.jadwals.store') }}" method="POST">
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
                <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">

                <div class="mb-3">
                    <label for="field-guru_mapel_kelas_id" class="block text-sm mb-1">Guru & Mata Pelajaran</label>
                    <select id="field-guru_mapel_kelas_id" name="guru_mapel_kelas_id" aria-invalid="{{ $errors->has('guru_mapel_kelas_id') ? 'true' : 'false' }}" @error('guru_mapel_kelas_id') aria-describedby="error-guru_mapel_kelas_id" @enderror class="w-full border rounded px-3 py-2 text-sm">
                        <option value="">- Pilih -</option>
                        @foreach ($penugasans as $p)
                            <option value="{{ $p->id }}"
                                {{ old('guru_mapel_kelas_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->mapel->nama }} — {{ $p->guru->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('guru_mapel_kelas_id')
                        <p id="error-guru_mapel_kelas_id" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="field-hari" class="block text-sm mb-1">Hari</label>
                    <select id="field-hari" name="hari" aria-invalid="{{ $errors->has('hari') ? 'true' : 'false' }}" @error('hari') aria-describedby="error-hari" @enderror class="w-full border rounded px-3 py-2 text-sm">
                        @foreach (['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'] as $hari)
                            <option value="{{ $hari }}" {{ old('hari') == $hari ? 'selected' : '' }}>
                                {{ ucfirst($hari) }}</option>
                        @endforeach
                    </select>
                    @error('hari')
                        <p id="error-hari" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="field-jam_mulai" class="block text-sm mb-1">Jam Mulai</label>
                        <input id="field-jam_mulai" type="time" name="jam_mulai" aria-invalid="{{ $errors->has('jam_mulai') ? 'true' : 'false' }}" @error('jam_mulai') aria-describedby="error-jam_mulai" @enderror value="{{ old('jam_mulai') }}"
                            class="w-full border rounded px-3 py-2 text-sm">
                        @error('jam_mulai')
                            <p id="error-jam_mulai" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="field-jam_selesai" class="block text-sm mb-1">Jam Selesai</label>
                        <input id="field-jam_selesai" type="time" name="jam_selesai" aria-invalid="{{ $errors->has('jam_selesai') ? 'true' : 'false' }}" @error('jam_selesai') aria-describedby="error-jam_selesai" @enderror value="{{ old('jam_selesai') }}"
                            class="w-full border rounded px-3 py-2 text-sm">
                        @error('jam_selesai')
                            <p id="error-jam_selesai" class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                </div>
            <div class="ews-form-actions"><button type="submit" class="ews-button ews-button-primary">
                    Simpan
                </button>
                <a href="{{ route('kurikulum.jadwals.index', ['kelas_id' => $kelas->id]) }}" class="ews-button ews-button-secondary">Batal</a>
            </div>
            </form>
        @endif
    </div>
@endsection
