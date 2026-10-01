@extends('layouts.guru_mapel')

@section('title', 'Buat Ujian')

@section('guru-mapel-content')
    <section class="ews-crud ews-crud-form" aria-labelledby="form-heading">
        <a href="{{ route('guru_mapel.ujian.index') }}" class="ews-back-link">← Kembali ke daftar ujian</a>
        <h1 id="form-heading">Buat Ujian</h1>
        <p>Pilih kelas dan isi informasi ujian. Soal ditambahkan setelah ujian disimpan.</p>
        @if ($penugasans->isEmpty())
            <div class="ews-notice bg-orange-50 text-orange-800 border-orange-200" role="status">Belum ada penugasan mengajar. Hubungi kurikulum untuk mengatur mata pelajaran dan kelas Anda.</div>
        @endif

        @if ($errors->any())
            <div class="ews-notice ews-notice-error" role="alert">
                <strong>Periksa kembali informasi ujian.</strong>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('guru_mapel.ujian.store') }}" method="POST">
            @csrf

            <div class="ews-fields">
                <div class="col-span-full">
                    <label for="penugasan" class="block">Mata pelajaran dan kelas</label>
                    <select id="penugasan" name="guru_mapel_kelas_id" required aria-invalid="{{ $errors->has('guru_mapel_kelas_id') ? 'true' : 'false' }}" @error('guru_mapel_kelas_id') aria-describedby="penugasan-error" @enderror>
                        <option value="">Pilih mata pelajaran dan kelas</option>
                        @foreach ($penugasans as $penugasan)
                            <option value="{{ $penugasan->id }}" @selected(old('guru_mapel_kelas_id') == $penugasan->id)>{{ $penugasan->mapel->nama }} - {{ $penugasan->kelas->nama }}</option>
                        @endforeach
                    </select>
                    @error('guru_mapel_kelas_id') <p id="penugasan-error" class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>
                <div class="col-span-full">
                    <label for="judul" class="block">Judul ujian</label>
                    <input id="judul" name="judul" type="text" required maxlength="150" value="{{ old('judul') }}" placeholder="Contoh: Ujian Harian Persamaan Linear" aria-invalid="{{ $errors->has('judul') ? 'true' : 'false' }}" @error('judul') aria-describedby="judul-error" @enderror>
                    @error('judul') <p id="judul-error" class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>
                <div class="col-span-full">
                    <label for="deskripsi" class="block">Deskripsi <span class="font-normal text-gray-500">(opsional)</span></label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" placeholder="Tuliskan petunjuk pengerjaan ujian." aria-invalid="{{ $errors->has('deskripsi') ? 'true' : 'false' }}" @error('deskripsi') aria-describedby="deskripsi-error" @enderror>{{ old('deskripsi') }}</textarea>
                    @error('deskripsi') <p id="deskripsi-error" class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="tanggal" class="block">Tanggal ujian</label>
                    <input id="tanggal" name="tanggal" type="date" required value="{{ old('tanggal') }}" aria-invalid="{{ $errors->has('tanggal') ? 'true' : 'false' }}" @error('tanggal') aria-describedby="tanggal-error" @enderror>
                    @error('tanggal') <p id="tanggal-error" class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="durasi_menit" class="block">Durasi pengerjaan (menit)</label>
                    <input id="durasi_menit" name="durasi_menit" type="number" min="5" max="300" step="1" required value="{{ old('durasi_menit') }}" placeholder="contoh: 60" aria-invalid="{{ $errors->has('durasi_menit') ? 'true' : 'false' }}" aria-describedby="durasi-hint{{ $errors->has('durasi_menit') ? ' durasi_menit-error' : '' }}">
                    <p id="durasi-hint" class="ews-field-hint">Minimal 5 menit, maksimal 300 menit. Timer berjalan sejak siswa memulai ujian.</p>
                    @error('durasi_menit') <p id="durasi_menit-error" class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="ews-form-actions">
                <button type="submit" class="ews-button ews-button-primary" @disabled($penugasans->isEmpty())>Simpan dan tambah soal</button>
                <a href="{{ route('guru_mapel.ujian.index') }}" class="ews-button ews-button-secondary">Batal</a>
            </div>
        </form>
    </section>
@endsection
