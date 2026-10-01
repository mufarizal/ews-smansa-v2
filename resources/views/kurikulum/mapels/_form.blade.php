@php
    $mapel = $mapel ?? null;
@endphp

<div class="mb-3">
    <label class="block text-sm mb-1">Nama Mata Pelajaran</label>
    <input type="text" name="nama" value="{{ old('nama', $mapel->nama ?? '') }}" placeholder="Contoh: Matematika"
        class="w-full border rounded px-3 py-2 text-sm">
    @error('nama')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-3">
    <label class="block text-sm mb-1">Kode (opsional)</label>
    <input type="text" name="kode" value="{{ old('kode', $mapel->kode ?? '') }}" placeholder="Contoh: MTK"
        class="w-full border rounded px-3 py-2 text-sm">
    @error('kode')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>
