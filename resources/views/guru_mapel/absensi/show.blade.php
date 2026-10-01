@extends('layouts.guru_mapel')

@section('title', 'Input Absensi')

@section('guru-mapel-content')
    <div class="ews-crud">
        <a href="{{ route('guru_mapel.absensi.index', ['tanggal' => $tanggal]) }}" class="ews-back-link">← Kembali ke jadwal mengajar</a>
        <h1 class="text-lg font-semibold mb-1">
            Input Absensi — {{ $jadwal->mapel->nama }} ({{ $jadwal->kelas->nama }})
        </h1>
        <p class="text-xs text-gray-500 mb-4">
            {{ \Illuminate\Support\Carbon::parse($tanggal)->translatedFormat('l, d M Y') }} —
            {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}
        </p>

        @if ($siswas->isEmpty())
            <p class="text-sm text-gray-500">Belum ada siswa di kelas ini.</p>
        @else
            <form action="{{ route('guru_mapel.absensi.store', $jadwal) }}" method="POST">
                @csrf
                @if ($errors->any())
                    <div class="ews-notice ews-notice-error" role="alert">
                        <strong>Periksa kembali absensi siswa.</strong>
                        <ul class="list-disc pl-5 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <p class="ews-table-hint">Periksa status setiap siswa sebelum menyimpan. Isi menit keterlambatan untuk status Terlambat.</p>
                <input type="hidden" name="tanggal" value="{{ $tanggal }}">

                <p class="ews-table-hint md:hidden">Geser tabel ke samping untuk melihat seluruh kolom.</p>
        <div class="ews-table-scroll" role="region" aria-label="Tabel data" tabindex="0"><table class="ews-absensi-table">
                    <thead>
                        <tr class="text-left border-b">
                            <th scope="col" class="py-2">NIS</th>
                            <th scope="col" class="py-2">Nama</th>
                            <th scope="col" class="py-2">Status</th>
                            <th scope="col" class="py-2">Menit Terlambat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($siswas as $siswa)
                            @php
                                $existing = $absensiSudahAda->get($siswa->id);
                                $statusTerpilih = old("data.{$siswa->id}.status", $existing->status ?? 'hadir');
                            @endphp
                            <tr class="border-b">
                                <td class="py-2">{{ $siswa->nis }}</td>
                                <td class="py-2">{{ $siswa->nama }}</td>
                                <td class="py-2">
                                    <select aria-label="Status kehadiran {{ $siswa->nama }}" name="data[{{ $siswa->id }}][status]"
                                        class="border rounded px-2 py-1 text-sm status-select"
                                        data-siswa-id="{{ $siswa->id }}">
                                        @foreach (['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpha' => 'Alpha', 'terlambat' => 'Terlambat'] as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ $statusTerpilih == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="py-2">
                                    <input aria-label="Menit terlambat {{ $siswa->nama }}" type="number" min="1" name="data[{{ $siswa->id }}][menit_terlambat]"
                                        value="{{ old("data.{$siswa->id}.menit_terlambat", $existing->menit_terlambat ?? '') }}"
                                        class="border rounded px-2 py-1 text-sm w-20 menit-terlambat-input"
                                        data-siswa-id="{{ $siswa->id }}"
                                        {{ $statusTerpilih !== 'terlambat' ? 'disabled' : '' }}>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table></div>

                <div class="ews-form-actions"><button type="submit" class="ews-button ews-button-primary">
                    Simpan absensi
                </button>
                <a href="{{ route('guru_mapel.absensi.index', ['tanggal' => $tanggal]) }}" class="ews-button ews-button-secondary">Batal</a></div>
            </form>
        @endif
    </div>

    <script>
        // Aktifkan/nonaktifkan input menit terlambat sesuai status yang dipilih (murni UX, validasi asli tetap di server)
        document.querySelectorAll('.status-select').forEach(function(select) {
            select.addEventListener('change', function() {
                var input = document.querySelector('.menit-terlambat-input[data-siswa-id="' + this.dataset
                    .siswaId + '"]');
                if (this.value === 'terlambat') {
                    input.disabled = false;
                } else {
                    input.disabled = true;
                    input.value = '';
                }
            });
        });
    </script>
@endsection
