@php
    $siswa = $siswa ?? null;
@endphp

<div class="mb-3">
    <label class="block text-sm mb-1">NIS</label>
    <input type="text" name="nis" value="{{ old('nis', $siswa->nis ?? '') }}"
        class="w-full border rounded px-3 py-2 text-sm">
    @error('nis')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-3">
    <label class="block text-sm mb-1">Nama Lengkap</label>
    <input type="text" name="nama" value="{{ old('nama', $siswa->nama ?? '') }}"
        class="w-full border rounded px-3 py-2 text-sm">
    @error('nama')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-3">
    <label class="block text-sm mb-1">Jenis Kelamin</label>
    <select name="jenis_kelamin" class="w-full border rounded px-3 py-2 text-sm">
        <option value="L" {{ old('jenis_kelamin', $siswa->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>
            Laki-laki</option>
        <option value="P" {{ old('jenis_kelamin', $siswa->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>
            Perempuan</option>
    </select>
    @error('jenis_kelamin')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-3">
    <label class="block text-sm mb-1">Kelas (opsional)</label>
    <select name="kelas_id" class="w-full border rounded px-3 py-2 text-sm">
        <option value="">- Belum ditentukan -</option>
        @foreach ($kelas as $item)
            <option value="{{ $item->id }}"
                {{ old('kelas_id', $siswa->kelas_id ?? '') == $item->id ? 'selected' : '' }}>
                {{ $item->nama }}
            </option>
        @endforeach
    </select>
    @error('kelas_id')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-3">
    <label class="block text-sm mb-1">Tanggal Lahir (opsional)</label>
    <input type="date" name="tanggal_lahir"
        value="{{ old('tanggal_lahir', isset($siswa) && $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('Y-m-d') : '') }}"
        class="w-full border rounded px-3 py-2 text-sm">
    @error('tanggal_lahir')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-3">
    <label class="block text-sm mb-1">Alamat (opsional)</label>
    <textarea name="alamat" rows="2" class="w-full border rounded px-3 py-2 text-sm">{{ old('alamat', $siswa->alamat ?? '') }}</textarea>
    @error('alamat')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-3">
    <label class="block text-sm mb-1">Nama Orang Tua (opsional)</label>
    <input type="text" name="nama_orang_tua" value="{{ old('nama_orang_tua', $siswa->nama_orang_tua ?? '') }}"
        class="w-full border rounded px-3 py-2 text-sm">
    @error('nama_orang_tua')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-3">
    <label class="block text-sm mb-1">No. HP Orang Tua (opsional)</label>
    <input type="text" name="no_hp_orang_tua" value="{{ old('no_hp_orang_tua', $siswa->no_hp_orang_tua ?? '') }}"
        class="w-full border rounded px-3 py-2 text-sm">
    @error('no_hp_orang_tua')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

@unless ($siswa)
    <div class="mb-3 bg-blue-50 text-xs text-blue-700 px-3 py-2 rounded">
        Password default akun ini: <strong>{{ config('school.default_password') }}</strong> — bisa diganti sendiri oleh
        siswa setelah login.
    </div>
@endunless
