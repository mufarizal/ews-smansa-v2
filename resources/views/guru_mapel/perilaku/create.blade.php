@extends('layouts.guru_mapel')

@section('title', 'Catat Perilaku Individual')

@section('guru-mapel-content')
    <section class="ews-crud ews-crud-form">
        <a href="{{ route('guru_mapel.perilaku.index') }}" class="ews-back-link">← Kembali ke daftar</a>
        <h1>Catat Perilaku Individual</h1>
        <p>Catat perilaku yang terjadi pada tanggal yang dipilih.</p>
        @if ($errors->any())
            <div class="ews-notice ews-notice-error" role="alert">
                <strong>Periksa kembali isian berikut.</strong>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if ($perilakus->isEmpty())
            <div class="ews-notice" role="status">Belum ada jenis perilaku. Hubungi Guru BK untuk menambahkan master perilaku.</div>
        @endif
        @if ($kelasList->isEmpty() || $kelasList->every(fn ($kelas) => $kelas->siswas->isEmpty()))
            <div class="ews-notice" role="status">Belum ada siswa pada kelas yang dapat Anda akses.</div>
        @endif
        <form action="{{ route('guru_mapel.perilaku.store') }}" method="POST">
            @csrf
            <div class="ews-fields">
                <div>
                    <label for="siswa_id" class="block">Siswa</label>
                    <select id="siswa_id" name="siswa_id" aria-invalid="{{ $errors->has('siswa_id') ? 'true' : 'false' }}" @error('siswa_id') aria-describedby="siswa_id-error" @enderror required>
                        <option value="">Pilih siswa</option>
                        @foreach ($kelasList as $kelas)
                            @foreach ($kelas->siswas as $siswa)
                                <option value="{{ $siswa->id }}" @selected(old('siswa_id') == $siswa->id)>{{ $siswa->nama }} ({{ $kelas->nama }})</option>
                            @endforeach
                        @endforeach
                    </select>
                    @error('siswa_id')
                        <p id="siswa_id-error" class="mt-2 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="perilaku_id" class="block">Jenis perilaku</label>
                    <select id="perilaku_id" name="perilaku_id" aria-invalid="{{ $errors->has('perilaku_id') ? 'true' : 'false' }}" @error('perilaku_id') aria-describedby="perilaku_id-error" @enderror required>
                        <option value="">Pilih jenis perilaku</option>
                        @foreach ($perilakus as $perilaku)
                            <option value="{{ $perilaku->id }}" @selected(old('perilaku_id', '') == $perilaku->id)>{{ $perilaku->nama }} ({{ $perilaku->jenis }}, {{ $perilaku->poin }} poin)</option>
                        @endforeach
                    </select>
                    @error('perilaku_id')
                        <p id="perilaku_id-error" class="mt-2 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="tanggal" class="block">Tanggal</label>
                    <input id="tanggal" name="tanggal" aria-invalid="{{ $errors->has('tanggal') ? 'true' : 'false' }}" @error('tanggal') aria-describedby="tanggal-error" @enderror type="date" value="{{ old('tanggal', now()->toDateString()) }}" required max="{{ now()->toDateString() }}">
                    @error('tanggal')
                        <p id="tanggal-error" class="mt-2 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="catatan" class="block">Catatan (opsional)</label>
                    <textarea id="catatan" name="catatan" aria-invalid="{{ $errors->has('catatan') ? 'true' : 'false' }}" @error('catatan') aria-describedby="catatan-error" @enderror rows="4">{{ old('catatan', '') }}</textarea>
                    @error('catatan')
                        <p id="catatan-error" class="mt-2 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="ews-form-actions">
                <button type="submit" class="ews-button ews-button-blue" @disabled($perilakus->isEmpty() || $kelasList->every(fn ($kelas) => $kelas->siswas->isEmpty()))>Simpan</button>
                <a href="{{ route('guru_mapel.perilaku.index') }}" class="ews-button ews-button-secondary">Batal</a>
            </div>
        </form>
    </section>
@endsection
