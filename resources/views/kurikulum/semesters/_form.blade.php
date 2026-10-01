@php
    $semester = $semester ?? null;
@endphp

<div class="mb-3">
    <label class="block text-sm mb-1">Nama Semester</label>
    <input type="text" name="nama" value="{{ old('nama', $semester->nama ?? '') }}"
        placeholder="Contoh: Ganjil 2025/2026" class="w-full border rounded px-3 py-2 text-sm">
    @error('nama')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-3">
    <label class="block text-sm mb-1">Jenis</label>
    <select name="jenis" class="w-full border rounded px-3 py-2 text-sm">
        <option value="ganjil" {{ old('jenis', $semester->jenis ?? '') == 'ganjil' ? 'selected' : '' }}>Ganjil</option>
        <option value="genap" {{ old('jenis', $semester->jenis ?? '') == 'genap' ? 'selected' : '' }}>Genap</option>
    </select>
    @error('jenis')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-3">
    <label class="block text-sm mb-1">Tahun Ajaran</label>
    <input type="text" name="tahun_ajaran" value="{{ old('tahun_ajaran', $semester->tahun_ajaran ?? '') }}"
        placeholder="Contoh: 2025/2026" class="w-full border rounded px-3 py-2 text-sm">
    @error('tahun_ajaran')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-3 grid grid-cols-2 gap-3">
    <div>
        <label class="block text-sm mb-1">Tanggal Mulai</label>
        <input type="date" name="tanggal_mulai"
            value="{{ old('tanggal_mulai', isset($semester) ? $semester->tanggal_mulai->format('Y-m-d') : '') }}"
            class="w-full border rounded px-3 py-2 text-sm">
        @error('tanggal_mulai')
            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="block text-sm mb-1">Tanggal Selesai</label>
        <input type="date" name="tanggal_selesai"
            value="{{ old('tanggal_selesai', isset($semester) ? $semester->tanggal_selesai->format('Y-m-d') : '') }}"
            class="w-full border rounded px-3 py-2 text-sm">
        @error('tanggal_selesai')
            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-3 flex items-center">
        <input type="checkbox" name="is_aktif" id="is_aktif" value="1"
            {{ old('is_aktif', $semester->is_aktif ?? false) ? 'checked' : '' }} class="mr-2">
        <label for="is_aktif" class="text-sm">
            Jadikan semester ini aktif
            <span class="text-gray-400 block text-xs">Semester lain yang sedang aktif akan otomatis
                dinonaktifkan.</span>
        </label>
    </div>
</div>
