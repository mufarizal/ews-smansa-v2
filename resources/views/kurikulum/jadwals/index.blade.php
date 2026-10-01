@extends('layouts.kurikulum')

@section('title', 'Jadwal Pelajaran')

@section('kurikulum-content')
    <div class="ews-crud">
        <div class="ews-crud-toolbar">
            <h1 class="text-lg font-semibold">Jadwal Pelajaran</h1>
            <div class="ews-actions">
                <a href="{{ route('kurikulum.jadwals.import.form') }}"
                    class="ews-button ews-button-secondary">
                    Import Excel
                </a>
                @if ($selectedKelasId)
                    <a href="{{ route('kurikulum.jadwals.create', ['kelas_id' => $selectedKelasId]) }}"
                        class="ews-button ews-button-primary">
                        + Tambah Jadwal
                    </a>
                @endif
            </div>
        </div>

        <form method="GET" action="{{ route('kurikulum.jadwals.index') }}" class="ews-filter">
            
            <div><label for="filter-kelas_id">Kelas</label>
                <select id="filter-kelas_id" name="kelas_id" class="border rounded px-3 py-2 text-sm">
                <option value="">- Semua Kelas -</option>
                @foreach ($kelasList as $kelas)
                    <option value="{{ $kelas->id }}" {{ $selectedKelasId == $kelas->id ? 'selected' : '' }}>
                        {{ $kelas->nama }}
                    </option>
                @endforeach
            </select>
        </div><button type="submit" class="ews-button ews-button-secondary">Terapkan</button></form>

        @if (!$selectedKelasId)
            <p class="text-sm text-gray-500 mb-4">Pilih kelas di atas untuk melihat detail jadwal dan menambah jadwal baru.
            </p>
        @endif

        <p class="ews-table-hint md:hidden">Geser tabel ke samping untuk melihat seluruh kolom.</p>
        <div class="ews-table-scroll" role="region" aria-label="Tabel data" tabindex="0"><table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b">
                    <th scope="col" class="py-2">Hari</th>
                    <th scope="col" class="py-2">Jam</th>
                    <th scope="col" class="py-2">Kelas</th>
                    <th scope="col" class="py-2">Mapel</th>
                    <th scope="col" class="py-2">Guru</th>
                    @if ($selectedKelasId)
                        <th scope="col" class="py-2">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($jadwals as $jadwal)
                    <tr class="border-b">
                        <td class="py-2">{{ ucfirst($jadwal->hari) }}</td>
                        <td class="py-2">{{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}
                        </td>
                        <td class="py-2">{{ $jadwal->kelas->nama }}</td>
                        <td class="py-2">{{ $jadwal->mapel->nama }}</td>
                        <td class="py-2">{{ $jadwal->guru->nama }}</td>
                        @if ($selectedKelasId)
                            <td class="py-2 space-x-2">
                                <a href="{{ route('kurikulum.jadwals.edit', $jadwal) }}"
                                    class="ews-button ews-button-secondary">Edit</a>
                                <form action="{{ route('kurikulum.jadwals.destroy', $jadwal) }}" method="POST"
                                    class="inline" onsubmit="return confirm('Yakin hapus jadwal ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ews-button ews-button-danger">Hapus</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-4 text-center text-gray-500">Belum ada jadwal.</td>
                    </tr>
                @endforelse
            </tbody>
        </table></div>
    </div>
@endsection
