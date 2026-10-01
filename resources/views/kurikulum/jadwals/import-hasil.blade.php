@extends('layouts.kurikulum')

@section('title', 'Hasil Import Jadwal')

@section('kurikulum-content')
    <div class="ews-crud">
        <h1 class="text-lg font-semibold mb-4">Hasil Import Jadwal</h1>

        <div class="mb-4 bg-green-50 text-green-700 text-sm px-4 py-2 rounded">
            {{ $berhasil }} jadwal berhasil ditambahkan.
        </div>

        @if (count($gagal) > 0)
            <div class="mb-4">
                <p class="text-sm font-medium text-red-700 mb-2">{{ count($gagal) }} baris gagal diimport:</p>
                <p class="ews-table-hint md:hidden">Geser tabel ke samping untuk melihat seluruh kolom.</p>
        <div class="ews-table-scroll" role="region" aria-label="Tabel data" tabindex="0"><table class="w-full text-sm">
                    <thead>
                        <tr class="text-left border-b">
                            <th scope="col" class="py-2">Baris Excel</th>
                            <th scope="col" class="py-2">Alasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gagal as $item)
                            <tr class="border-b">
                                <td class="py-2">{{ $item['baris'] }}</td>
                                <td class="py-2 text-red-600">{{ $item['pesan'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table></div>
            </div>
        @endif

        <a href="{{ route('kurikulum.jadwals.index') }}" class="ews-button ews-button-secondary">← Kembali ke Jadwal</a>
    </div>
@endsection
