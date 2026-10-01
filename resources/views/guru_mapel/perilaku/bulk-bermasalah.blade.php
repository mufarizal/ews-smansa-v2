@extends('layouts.guru_mapel')

@section('title', 'Catat Kelas Bermasalah')

@section('guru-mapel-content')
    <section class="ews-crud ews-crud-form">
        <a href="{{ route('guru_mapel.perilaku.index') }}" class="ews-back-link">← Kembali ke daftar</a>
        <h1>Catat Kelas Bermasalah</h1>
        <p>Pilih kelas, jenis perilaku negatif, dan siswa yang terlibat.</p>
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
        <form action="{{ route('guru_mapel.perilaku.bulk-bermasalah.form') }}" method="GET" class="mb-6 rounded-lg border border-gray-200 bg-gray-50 p-4">
            <label for="filter-kelas" class="block">Pilih kelas</label>
            <select id="filter-kelas" name="kelas_id" class="w-full" onchange="this.form.submit()">
                <option value="">Pilih kelas</option>
                @foreach ($kelasList as $kelas)
                    <option value="{{ $kelas->id }}" @selected($kelasTerpilih?->id == $kelas->id)>{{ $kelas->nama }}</option>
                @endforeach
            </select>
            <noscript>
                <button type="submit" class="ews-button ews-button-secondary mt-3">Tampilkan siswa</button>
            </noscript>
        </form>
        @if ($kelasTerpilih)
            <h2 class="mb-4 text-lg font-semibold">Siswa kelas {{ $kelasTerpilih->nama }}</h2>
            @if ($perilakuNegatif->isEmpty())
                <div class="ews-notice">Belum ada jenis perilaku negatif. Hubungi Guru BK untuk menambahkannya.</div>
            @endif
            <form action="{{ route('guru_mapel.perilaku.bulk-bermasalah.store') }}" method="POST">
                @csrf
                <input type="hidden" name="kelas_id" value="{{ $kelasTerpilih->id }}">
                <div class="ews-fields">
                    <div>
                        <label for="perilaku_id" class="block">Jenis perilaku negatif</label>
                        <select id="perilaku_id" name="perilaku_id" aria-invalid="{{ $errors->has('perilaku_id') ? 'true' : 'false' }}" @error('perilaku_id') aria-describedby="perilaku_id-error" @enderror required>
                            <option value="">Pilih jenis perilaku</option>
                            @foreach ($perilakuNegatif as $perilaku)
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
                </div>
                <fieldset class="mt-6">
                    <legend class="mb-3 font-semibold">Pilih siswa yang dicatat</legend>
                    <p class="mb-4 text-sm text-gray-600">Centang minimal satu siswa. Catatan hanya diterapkan pada siswa yang dipilih.</p>
                    <div class="grid gap-3 sm:grid-cols-2">
                        @forelse ($siswas as $siswa)
                            <label for="siswa-{{ $siswa->id }}" class="flex items-center">
                                <input id="siswa-{{ $siswa->id }}" type="checkbox" name="siswa_ids[]" value="{{ $siswa->id }}" @checked(in_array($siswa->id, (array) old('siswa_ids', [])))>
                                <span class="min-w-0">
                                    <span class="block break-words font-medium">{{ $siswa->nama }}</span>
                                    <span class="text-xs text-gray-500">NIS {{ $siswa->nis }}</span>
                                </span>
                            </label>
                        @empty
                            <p class="col-span-full rounded-lg border border-dashed border-gray-300 p-5 text-sm text-gray-600">Belum ada siswa pada kelas ini.</p>
                        @endforelse
                    </div>
                    @error('siswa_ids')
                        <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                    @error('siswa_ids.*')
                        <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </fieldset>
                <div class="ews-form-actions">
                    <button type="submit" class="ews-button ews-button-orange" @disabled($siswas->isEmpty() || $perilakuNegatif->isEmpty())>Simpan</button>
                    <a href="{{ route('guru_mapel.perilaku.index') }}" class="ews-button ews-button-secondary">Batal</a>
                </div>
            </form>
        @else
            <div class="rounded-lg border border-dashed border-gray-300 p-6 text-center text-sm text-gray-600">Pilih kelas untuk menampilkan daftar siswa.</div>
        @endif
    </section>
@endsection
