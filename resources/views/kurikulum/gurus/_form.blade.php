@php
    $guru = $guru ?? null;
@endphp

<div class="mb-3">
    <label class="block text-sm mb-1">NIP</label>
    <input type="text" name="nip" value="{{ old('nip', $guru->nip ?? '') }}"
        class="w-full border rounded px-3 py-2 text-sm">
    @error('nip')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-3">
    <label class="block text-sm mb-1">Nama Lengkap</label>
    <input type="text" name="nama" value="{{ old('nama', $guru->nama ?? '') }}"
        class="w-full border rounded px-3 py-2 text-sm">
    @error('nama')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-3">
    <label class="block text-sm mb-1">Jenis Kelamin</label>
    <select name="jenis_kelamin" class="w-full border rounded px-3 py-2 text-sm">
        <option value="L" {{ old('jenis_kelamin', $guru->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>Laki-laki
        </option>
        <option value="P" {{ old('jenis_kelamin', $guru->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>Perempuan
        </option>
    </select>
    @error('jenis_kelamin')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-3">
    <label class="block text-sm mb-1">No. HP (opsional)</label>
    <input type="text" name="no_hp" value="{{ old('no_hp', $guru->no_hp ?? '') }}"
        class="w-full border rounded px-3 py-2 text-sm">
    @error('no_hp')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-3">
    <label class="block text-sm mb-1">Alamat (opsional)</label>
    <textarea name="alamat" rows="2" class="w-full border rounded px-3 py-2 text-sm">{{ old('alamat', $guru->alamat ?? '') }}</textarea>
    @error('alamat')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

@unless ($guru)
    <div class="mb-3 bg-blue-50 text-xs text-blue-700 px-3 py-2 rounded">
        Password default akun ini: <strong>{{ config('school.default_password') }}</strong> — bisa diganti sendiri oleh
        guru setelah login.
    </div>
@endunless
