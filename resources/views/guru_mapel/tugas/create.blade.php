@extends('layouts.guru_mapel')

@section('title', 'Buat Tugas')

@section('guru-mapel-content')
    <section class="ews-crud ews-crud-form" aria-labelledby="form-heading">
        <a href="{{ route('guru_mapel.tugas.index') }}" class="ews-back-link">← Kembali ke daftar tugas</a>
        <h1 id="form-heading">Buat Tugas</h1>
        <p>Pilih kelas dan isi informasi tugas. Soal ditambahkan setelah tugas disimpan.</p>
        @if ($penugasans->isEmpty())
            <div class="ews-notice bg-orange-50 text-orange-800 border-orange-200" role="status">Belum ada penugasan mengajar. Hubungi kurikulum untuk mengatur mata pelajaran dan kelas Anda.</div>
        @endif

        @if ($errors->any())
            <div class="ews-notice ews-notice-error" role="alert">
                <strong>Periksa kembali informasi tugas.</strong>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('guru_mapel.tugas.store') }}" method="POST">
            @csrf
            
            <div class="ews-fields">
                <div class="sm:col-span-2">
                    <label for="penugasan" class="block">Mata pelajaran dan kelas</label>
                    <select id="penugasan" name="guru_mapel_kelas_id" required aria-invalid="{{ $errors->has('guru_mapel_kelas_id') ? 'true' : 'false' }}" @error('guru_mapel_kelas_id') aria-describedby="penugasan-error" @enderror>
                        <option value="">Pilih mata pelajaran dan kelas</option>
                        @foreach ($penugasans as $penugasan)
                            <option value="{{ $penugasan->id }}" @selected(old('guru_mapel_kelas_id') == $penugasan->id)>{{ $penugasan->mapel->nama }} - {{ $penugasan->kelas->nama }}</option>
                        @endforeach
                    </select>
                    @error('guru_mapel_kelas_id') <p id="penugasan-error" class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="judul" class="block">Judul tugas</label>
                    <input id="judul" name="judul" type="text" required maxlength="255" value="{{ old('judul') }}" placeholder="Contoh: Latihan persamaan linear" aria-invalid="{{ $errors->has('judul') ? 'true' : 'false' }}" @error('judul') aria-describedby="judul-error" @enderror>
                    @error('judul') <p id="judul-error" class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="deskripsi" class="block">Deskripsi <span class="font-normal text-gray-500">(opsional)</span></label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" placeholder="Tuliskan petunjuk pengerjaan tugas." aria-invalid="{{ $errors->has('deskripsi') ? 'true' : 'false' }}" @error('deskripsi') aria-describedby="deskripsi-error" @enderror>{{ old('deskripsi') }}</textarea>
                    @error('deskripsi') <p id="deskripsi-error" class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="tanggal_mulai" class="block">Tanggal mulai</label>
                    <input id="tanggal_mulai" name="tanggal_mulai" type="date" required value="{{ old('tanggal_mulai') }}" aria-invalid="{{ $errors->has('tanggal_mulai') ? 'true' : 'false' }}" @error('tanggal_mulai') aria-describedby="tanggal_mulai-error" @enderror>
                    @error('tanggal_mulai') <p id="tanggal_mulai-error" class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="tanggal_selesai" class="block">Tanggal selesai</label>
                    <input id="tanggal_selesai" name="tanggal_selesai" type="date" required value="{{ old('tanggal_selesai') }}" aria-invalid="{{ $errors->has('tanggal_selesai') ? 'true' : 'false' }}" @error('tanggal_selesai') aria-describedby="tanggal_selesai-error" @enderror>
                    @error('tanggal_selesai') <p id="tanggal_selesai-error" class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="ews-form-actions">
                <button type="submit" class="ews-button ews-button-primary" @disabled($penugasans->isEmpty())>Simpan dan tambah soal</button>
                <a href="{{ route('guru_mapel.tugas.index') }}" class="ews-button ews-button-secondary">Batal</a>
            </div>
        </form>
    </section>
@endsection
