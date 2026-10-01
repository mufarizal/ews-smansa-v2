@php
    $kelas = $kelas ?? null;
@endphp

<div class="mb-3">
    <label class="block text-sm mb-1">Nama Kelas</label>
    <input type="text" name="nama" value="{{ old('nama', $kelas->nama ?? '') }}" placeholder="Contoh: X-A"
        class="w-full border rounded px-3 py-2 text-sm">
    @error('nama')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-3">
    <label class="block text-sm mb-1">Tingkat</label>
    <input type="text" name="tingkat" value="{{ old('tingkat', $kelas->tingkat ?? '') }}"
        placeholder="Contoh: X, XI, XII" class="w-full border rounded px-3 py-2 text-sm">
    @error('tingkat')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-3">
    <label class="block text-sm mb-1">Wali Kelas (opsional)</label>
    <select name="wali_kelas_id" class="w-full border rounded px-3 py-2 text-sm">
        <option value="">- Belum ditentukan -</option>
        @foreach ($gurus as $guru)
            <option value="{{ $guru->id }}"
                {{ old('wali_kelas_id', $kelas->wali_kelas_id ?? '') == $guru->id ? 'selected' : '' }}>
                {{ $guru->nama }}
            </option>
        @endforeach
    </select>
    @error('wali_kelas_id')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
    @if ($gurus->isEmpty())
        <p class="text-xs text-gray-400 mt-1">Belum ada data guru — tambahkan guru dulu untuk bisa pilih wali kelas.</p>
    @endif
</div>
