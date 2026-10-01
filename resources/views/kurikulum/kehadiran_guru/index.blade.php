@extends('layouts.kurikulum')

@section('title', 'Kehadiran Guru')

@section('kurikulum-content')
    <div class="ews-crud">
        <div class="ews-crud-toolbar">
            <h1 class="text-lg font-semibold">Kehadiran Guru</h1>
            <form method="GET" action="{{ route('kurikulum.kehadiran-guru.index') }}" class="ews-filter">
                <div><label for="filter-tanggal">Tanggal kehadiran</label>
                <input type="date" id="filter-tanggal" name="tanggal" value="{{ $tanggal }}"
                    class="border rounded px-3 py-2 text-sm">
            </div><button type="submit" class="ews-button ews-button-secondary">Terapkan</button></form>
        </div>

        <p class="ews-table-hint md:hidden">Geser tabel ke samping untuk melihat seluruh kolom.</p>
        <div class="ews-table-scroll" role="region" aria-label="Tabel data" tabindex="0"><table class="w-full text-sm ">
            <thead>
                <tr class="bg-gray-50 text-left">
                    <th scope="col" class="py-2 px-4 border-b font-semibold text-gray-700">Guru</th>
                    <th scope="col" class="py-2 px-4 border-b font-semibold text-gray-700 text-center">Pagi</th>
                    <th scope="col" class="py-2 px-4 border-b font-semibold text-gray-700 text-center">Sore</th>
                    <th scope="col" class="py-2 px-4 border-b font-semibold text-gray-700">Keterangan</th>
                    <th scope="col" class="py-2 px-4 border-b font-semibold text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kehadirans as $guruId => $items)
                    @php
                        $pagi = $items->firstWhere('sesi', 'pagi');
                        $sore = $items->firstWhere('sesi', 'sore');
                        $guruNama = $items->first()->guru->nama ?? '-';

                        // Gabungkan keterangan
                        $keteranganParts = [];
                        if ($pagi && $pagi->keterangan) {
                            $keteranganParts[] = 'Pagi: ' . $pagi->keterangan;
                        }
                        if ($sore && $sore->keterangan) {
                            $keteranganParts[] = 'Sore: ' . $sore->keterangan;
                        }
                        $keteranganGabungan = !empty($keteranganParts) ? implode(' | ', $keteranganParts) : '-';

                        // Gabungkan aksi
                        $aksiParts = [];
                        if ($pagi) {
                            $aksiParts[] =
                                '<a href="' .
                                route('kurikulum.kehadiran-guru.edit', $pagi) .
                                '" class="ews-button ews-button-secondary">Edit Pagi</a>';
                        }
                        if ($sore) {
                            $aksiParts[] =
                                '<a href="' .
                                route('kurikulum.kehadiran-guru.edit', $sore) .
                                '" class="ews-button ews-button-secondary">Edit Sore</a>';
                        }
                        $aksiGabungan = !empty($aksiParts) ? implode(' ', $aksiParts) : '-';
                    @endphp
                    <tr class=" hover:bg-gray-50">
                        <td class="py-3 px-4 font-medium text-gray-900">{{ $guruNama }}</td>
                        <td class="py-3 px-4 text-center align-middle">
                            @if ($pagi)
                                @php
                                    $warnaPagi = match ($pagi->status) {
                                        'hadir' => 'bg-green-100 text-green-700',
                                        'izin' => 'bg-blue-100 text-blue-700',
                                        'sakit' => 'bg-yellow-100 text-yellow-700',
                                        'alpha' => 'bg-red-100 text-red-700',
                                    };
                                @endphp
                                <div class="flex flex-col items-center gap-1">
                                    <span class="{{ $warnaPagi }} text-xs px-2 py-1 rounded font-medium">
                                        {{ ucfirst($pagi->status) }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ $pagi->waktu_absen?->format('H:i') ?? '-' }}
                                    </span>
                                </div>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center align-middle">
                            @if ($sore)
                                @php
                                    $warnaSore = match ($sore->status) {
                                        'hadir' => 'bg-green-100 text-green-700',
                                        'izin' => 'bg-blue-100 text-blue-700',
                                        'sakit' => 'bg-yellow-100 text-yellow-700',
                                        'alpha' => 'bg-red-100 text-red-700',
                                    };
                                @endphp
                                <div class="flex flex-col items-center gap-1">
                                    <span class="{{ $warnaSore }} text-xs px-2 py-1 rounded font-medium">
                                        {{ ucfirst($sore->status) }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ $sore->waktu_absen?->format('H:i') ?? '-' }}
                                    </span>
                                </div>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-xs text-gray-600 align-top">{!! $keteranganGabungan !!}</td>
                        <td class="py-3 px-4 text-xs align-top">{!! $aksiGabungan !!}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-4 text-center text-gray-500">Belum ada data kehadiran pada tanggal ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table></div>
        <div class="mt-4">
            {{ $guruIds->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
