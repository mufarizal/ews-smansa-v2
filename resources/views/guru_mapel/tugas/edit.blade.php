@extends('layouts.guru_mapel')

@section('title', 'Edit Tugas')

@section('guru-mapel-content')
    <section class="ews-crud ews-crud-form" aria-labelledby="form-heading">
        <a href="{{ route('guru_mapel.tugas.show', $tugas) }}" class="ews-back-link">← Kembali ke detail tugas</a>
        <h1 id="form-heading">Edit Tugas</h1>
        <p>Perbarui informasi dan periode pengerjaan tugas.</p>

        @if ($errors->any())
            <div class="ews-notice ews-notice-error" role="alert">
                <strong>Periksa kembali informasi tugas.</strong>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('guru_mapel.tugas.update', $tugas) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="ews-fields">
                <div class="sm:col-span-2">
                    <label for="judul" class="block">Judul tugas</label>
                    <input id="judul" name="judul" type="text" required maxlength="150" value="{{ old('judul', $tugas->judul) }}" placeholder="Contoh: Latihan persamaan linear" aria-invalid="{{ $errors->has('judul') ? 'true' : 'false' }}" @error('judul') aria-describedby="judul-error" @enderror>
                    @error('judul') <p id="judul-error" class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="deskripsi" class="block">Deskripsi <span class="font-normal text-gray-500">(opsional)</span></label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" placeholder="Tuliskan petunjuk pengerjaan tugas." aria-invalid="{{ $errors->has('deskripsi') ? 'true' : 'false' }}" @error('deskripsi') aria-describedby="deskripsi-error" @enderror>{{ old('deskripsi', $tugas->deskripsi) }}</textarea>
                    @error('deskripsi') <p id="deskripsi-error" class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="tanggal_mulai" class="block">Tanggal mulai</label>
                    <input id="tanggal_mulai" name="tanggal_mulai" type="date" required value="{{ old('tanggal_mulai', $tugas->tanggal_mulai->format('Y-m-d')) }}" aria-invalid="{{ $errors->has('tanggal_mulai') ? 'true' : 'false' }}" @error('tanggal_mulai') aria-describedby="tanggal_mulai-error" @enderror>
                    @error('tanggal_mulai') <p id="tanggal_mulai-error" class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="tanggal_selesai" class="block">Tanggal selesai</label>
                    <input id="tanggal_selesai" name="tanggal_selesai" type="date" required value="{{ old('tanggal_selesai', $tugas->tanggal_selesai->format('Y-m-d')) }}" aria-invalid="{{ $errors->has('tanggal_selesai') ? 'true' : 'false' }}" @error('tanggal_selesai') aria-describedby="tanggal_selesai-error" @enderror>
                    @error('tanggal_selesai') <p id="tanggal_selesai-error" class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="ews-form-actions">
                <button type="submit" class="ews-button ews-button-primary" >Simpan perubahan</button>
                <a href="{{ route('guru_mapel.tugas.show', $tugas) }}" class="ews-button ews-button-secondary">Batal</a>
            </div>
        </form>
    </section>
@endsection
