@extends('layouts.guru_mapel')

@section('title', 'Absensi Mapel')

@section('guru-mapel-content')
    <div class="ews-crud">
        <div class="ews-crud-toolbar">
            <h1 class="text-lg font-semibold">Absensi Mapel</h1>
            <form method="GET" action="{{ route('guru_mapel.absensi.index') }}" class="ews-filter">
                <div><label for="filter-tanggal">Tanggal mengajar</label>
                <input type="date" id="filter-tanggal" name="tanggal" value="{{ $tanggal }}"
                    min="{{ now()->subDays(7)->toDateString() }}" max="{{ now()->toDateString() }}"
                    class="border rounded px-3 py-2 text-sm">
            </div><button type="submit" class="ews-button ews-button-secondary">Terapkan</button></form>
        </div>

        @if ($namaHari === 'minggu')
            <p class="text-sm text-gray-500">Tidak ada jadwal — hari Minggu libur.</p>
        @elseif ($jadwals->isEmpty())
            <p class="text-sm text-gray-500">Tidak ada jadwal mengajar Anda pada tanggal ini ({{ ucfirst($namaHari) }}).</p>
        @else
            <p class="ews-table-hint md:hidden">Geser tabel ke samping untuk melihat seluruh kolom.</p>
        <div class="ews-table-scroll" role="region" aria-label="Tabel data" tabindex="0"><table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b">
                        <th scope="col" class="py-2">Jam</th>
                        <th scope="col" class="py-2">Kelas</th>
                        <th scope="col" class="py-2">Mapel</th>
                        <th scope="col" class="py-2">Status Input</th>
                        <th scope="col" class="py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jadwals as $jadwal)
                        <tr class="border-b">
                            <td class="py-2">{{ substr($jadwal->jam_mulai, 0, 5) }} -
                                {{ substr($jadwal->jam_selesai, 0, 5) }}</td>
                            <td class="py-2">{{ $jadwal->kelas->nama }}</td>
                            <td class="py-2">{{ $jadwal->mapel->nama }}</td>
                            <td class="py-2">
                                @if ($jadwal->status_input === 'lengkap')
                                    <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded">Lengkap</span>
                                @elseif ($jadwal->status_input === 'sebagian')
                                    <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-1 rounded">Sebagian</span>
                                @else
                                    <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded">Belum diisi</span>
                                @endif
                            </td>
                            <td class="py-2">
                                <a href="{{ route('guru_mapel.absensi.show', ['jadwal' => $jadwal, 'tanggal' => $tanggal]) }}"
                                    class="ews-button ews-button-secondary">
                                    {{ $jadwal->status_input === 'belum' ? 'Input Absensi' : 'Lihat / Edit' }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table></div>
        @endif
    </div>
@endsection
