@extends('layouts.guru_mapel')

@section('title', 'Edit Ujian')

@section('guru-mapel-content')
    <section class="ews-crud ews-crud-form" aria-labelledby="form-heading">
        <a href="{{ route('guru_mapel.ujian.show', $ujian) }}" class="ews-back-link">← Kembali ke detail ujian</a>
        <h1 id="form-heading">Edit Ujian</h1>
        <p>Perbarui informasi, tanggal, dan durasi pengerjaan ujian.</p>

        @if ($errors->any())
            <div class="ews-notice ews-notice-error" role="alert">
                <strong>Periksa kembali informasi ujian.</strong>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('guru_mapel.ujian.update', $ujian) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="ews-fields">
                <div class="col-span-full">
                    <label for="judul" class="block">Judul ujian</label>
                    <input id="judul" name="judul" type="text" required maxlength="150" value="{{ old('judul', $ujian->judul) }}" placeholder="Contoh: Ujian Harian Persamaan Linear" aria-invalid="{{ $errors->has('judul') ? 'true' : 'false' }}" @error('judul') aria-describedby="judul-error" @enderror>
                    @error('judul') <p id="judul-error" class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>
                <div class="col-span-full">
                    <label for="deskripsi" class="block">Deskripsi <span class="font-normal text-gray-500">(opsional)</span></label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" placeholder="Tuliskan petunjuk pengerjaan ujian." aria-invalid="{{ $errors->has('deskripsi') ? 'true' : 'false' }}" @error('deskripsi') aria-describedby="deskripsi-error" @enderror>{{ old('deskripsi', $ujian->deskripsi) }}</textarea>
                    @error('deskripsi') <p id="deskripsi-error" class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="tanggal" class="block">Tanggal ujian</label>
                    <input id="tanggal" name="tanggal" type="date" required value="{{ old('tanggal', $ujian->tanggal->format('Y-m-d')) }}" aria-invalid="{{ $errors->has('tanggal') ? 'true' : 'false' }}" @error('tanggal') aria-describedby="tanggal-error" @enderror>
                    @error('tanggal') <p id="tanggal-error" class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="durasi_menit" class="block">Durasi pengerjaan (menit)</label>
                    <input id="durasi_menit" name="durasi_menit" type="number" min="5" max="300" step="1" required value="{{ old('durasi_menit', $ujian->durasi_menit) }}" placeholder="contoh: 60" aria-invalid="{{ $errors->has('durasi_menit') ? 'true' : 'false' }}" aria-describedby="durasi-hint{{ $errors->has('durasi_menit') ? ' durasi_menit-error' : '' }}">
                    <p id="durasi-hint" class="ews-field-hint">Minimal 5 menit, maksimal 300 menit. Timer berjalan sejak siswa memulai ujian.</p>
                    @error('durasi_menit') <p id="durasi_menit-error" class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="ews-form-actions">
                <button type="submit" class="ews-button ews-button-primary">Simpan perubahan</button>
                <a href="{{ route('guru_mapel.ujian.show', $ujian) }}" class="ews-button ews-button-secondary">Batal</a>
            </div>
        </form>
    </section>
@endsection
