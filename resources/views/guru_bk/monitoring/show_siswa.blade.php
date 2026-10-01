@extends('layouts.guru_bk')

@section('title', 'Detail Monitoring Siswa')

@section('guru-bk-content')
    @php
        $kategoriStyles = [
            'aman' => 'bg-green-100 text-green-700',
            'perhatian' => 'bg-yellow-100 text-yellow-700',
            'binaan' => 'bg-red-100 text-red-700',
        ];
        $kategoriLabels = ['aman' => 'Aman', 'perhatian' => 'Perhatian', 'binaan' => 'Binaan'];
    @endphp
    @php
        $hasil = $hasilTerkini;
    @endphp
    <div class="space-y-6">
        <section class="ews-crud">
            <a class="ews-back-link" href="{{ route('guru_bk.monitoring.show', $kelas) }}">← Kembali ke daftar siswa</a>
            <header class="ews-crud-toolbar">
                <div><h1>{{ $siswa->nama }}</h1><p class="mt-2 text-sm text-gray-600">NIS {{ $siswa->nis }}</p></div>
                <span class="inline-flex rounded px-2.5 py-1 text-sm font-medium {{ $kategoriStyles[$hasil?->kategori] ?? 'bg-gray-100 text-gray-700' }}">{{ $kategoriLabels[$hasil?->kategori] ?? 'Belum ada data' }}</span>
            </header>
            <p class="text-sm text-gray-600">Skor akhir</p>
            <p class="mt-2 text-4xl font-semibold tabular-nums">{{ $hasil?->skor_akhir === null ? '-' : number_format((float) $hasil->skor_akhir, 2, ',', '.') }}</p>
            @if (!$hasil)
                <p class="mt-4 text-sm text-gray-600">Belum ada hasil monitoring untuk siswa ini.</p>
            @elseif ($hasil->data_tidak_lengkap)
                <span class="mt-4 inline-flex rounded bg-yellow-100 px-2.5 py-1 text-xs font-medium text-yellow-700">⚠ Data Tidak Lengkap</span>
            @endif
        </section>
        <section class="ews-crud">
            <h2 class="ews-card-title">Breakdown skor</h2>
            @php
                // Keep compatibility with both the requested aliases and the current model fields.
                $breakdown = [
                    ['Akademik', $hasil?->c1_akademik, $hasil?->r1_akademik ?? $hasil?->r3_akademik, 'Ringkasan data akademik yang tersedia.'],
                    ['Absensi', $hasil?->c2_absensi, $hasil?->r2_absensi ?? $hasil?->r1_absensi, 'Ringkasan data kehadiran siswa.'],
                    ['Perilaku', $hasil?->c3_perilaku, $hasil?->r3_perilaku ?? $hasil?->r2_perilaku, 'Ringkasan catatan perilaku positif dan negatif.'],
                ];
            @endphp
            <div class="grid gap-6 md:grid-cols-3">
                @foreach ($breakdown as [$label, $mentah, $normalisasi, $penjelasan])
                    <article class="ews-crud">
                        <h3 class="ews-card-title">{{ $label }}</h3>
                        <dl class="space-y-3 text-sm">
                            <div><dt class="text-gray-600">Nilai mentah</dt><dd class="mt-1 text-xl font-semibold tabular-nums">{{ $mentah === null ? '-' : number_format((float) $mentah, 2, ',', '.') }}</dd></div>
                            <div><dt class="text-gray-600">Nilai ternormalisasi</dt><dd class="mt-1 font-semibold tabular-nums">{{ $normalisasi === null ? '-' : number_format((float) $normalisasi, 4, ',', '.') }}</dd></div>
                        </dl>
                        <p class="mt-4 text-xs leading-6 text-gray-600">{{ $penjelasan }} Nilai ternormalisasi adalah nilai yang disesuaikan untuk perhitungan skor akhir.</p>
                    </article>
                @endforeach
            </div>
            @if ($hasil)
                <p class="mt-4 text-sm text-gray-600">Total perilaku positif: {{ $hasil->total_perilaku_positif ?? '-' }} · Total perilaku negatif: {{ $hasil->total_perilaku_negatif ?? '-' }}</p>
            @endif
        </section>
        @if ($hasil?->input_metadata)
            <section class="ews-crud" aria-labelledby="metadata-heading">
                <h2 id="metadata-heading" class="ews-card-title">Kelengkapan data input</h2>
                <div class="rounded bg-yellow-100 p-4 text-sm text-yellow-700" role="status">
                    <p class="font-semibold">⚠ Data input perlu ditinjau</p>
                    <p class="mt-2">Informasi berikut membantu menjelaskan data yang belum lengkap pada tanggal perhitungan.</p>
                    @foreach (['akademik' => 'Kehadiran guru / akademik', 'absensi_mapel' => 'Input absensi mapel'] as $key => $label)
                        @php
                            $entries = $hasil->input_metadata[$key] ?? ($key === 'absensi_mapel' ? ($hasil->input_metadata['absensi'] ?? []) : []);
                        @endphp
                        @if (count($entries))
                            <h3 class="mt-4 font-semibold">{{ $label }}</h3>
                            <ul class="mt-2 list-disc space-y-2 pl-5">
                                @foreach ($entries as $entry)
                                    <li>{{ $entry['guru_nama'] ?? 'Guru' }} — {{ $entry['mapel'] ?? '-' }}: {{ ucfirst(str_replace('_', ' ', $entry['status'] ?? 'Belum ada keterangan')) }}</li>
                                @endforeach
                            </ul>
                        @endif
                    @endforeach
                </div>
            </section>
        @endif
    <section class="ews-crud" aria-labelledby="trend-heading">
        <h2 id="trend-heading" class="ews-card-title">Tren skor</h2>
        <p class="mb-4 text-sm text-gray-600">Perubahan skor dari waktu ke waktu berdasarkan data yang tersedia.</p>
        @if ($trend->isNotEmpty())
            @php
                $chartRows = $trend->sortBy('tanggal_hitung')->values();
                $chartLabels = $chartRows->map(fn ($row) => $row->tanggal_hitung->format('d M Y'))->all();
                $chartScores = $chartRows->map(fn ($row) => $row->skor_akhir === null ? null : (float) $row->skor_akhir)->all();
            @endphp
            <div class="relative h-64 min-w-0 sm:h-72" id="trend-chart-container" hidden>
                <canvas id="trend-chart" role="img" aria-label="Grafik garis tren skor. Data lengkap tersedia pada tabel di bawah."></canvas>
            </div>
            <p id="trend-fallback" class="text-sm text-gray-600" role="status">Data tren tersedia dalam tabel berikut jika grafik belum dimuat.</p>
            <details class="mt-4" open id="trend-data">
                <summary class="cursor-pointer py-2 text-sm font-semibold">Data tren skor</summary>
                <div class="ews-table-scroll" role="region" aria-label="Data tren skor" tabindex="0">
                    <table>
                        <thead><tr><th scope="col">Tanggal</th><th scope="col">Skor akhir</th></tr></thead>
                        <tbody>
                            @foreach ($chartRows as $row)
                                <tr><td>{{ $row->tanggal_hitung->format('d M Y') }}</td><td>{{ $row->skor_akhir === null ? '-' : number_format((float) $row->skor_akhir, 2, ',', '.') }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </details>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
            <script>
                (() => {
                    if (typeof Chart === 'undefined') return;
                    const container = document.getElementById('trend-chart-container');
                    const canvas = document.getElementById('trend-chart');
                    const textStyle = getComputedStyle(container);
                    const cardStyle = getComputedStyle(container.closest('section'));
                    const labels = {{ Illuminate\Support\Js::from($chartLabels) }};
                    const scores = {{ Illuminate\Support\Js::from($chartScores) }};
                    // Reuse the existing primary button and page text colors.
                    const token = document.createElement('span');
                    token.className = 'ews-button ews-button-blue';
                    token.hidden = true;
                    container.appendChild(token);
                    const lineColor = getComputedStyle(token).backgroundColor;
                    token.remove();
                    container.hidden = false;
                    try {
                        new Chart(canvas, {
                            type: 'line',
                            data: { labels, datasets: [{ label: 'Skor akhir', data: scores, borderColor: lineColor, backgroundColor: lineColor, borderWidth: 2, pointRadius: 3, fill: false, tension: 0 }] },
                            options: {
                                responsive: true, maintainAspectRatio: false, animation: false,
                                color: textStyle.color,
                                font: { family: textStyle.fontFamily },
                                plugins: { legend: { display: false }, tooltip: { backgroundColor: textStyle.color, titleColor: cardStyle.backgroundColor, bodyColor: cardStyle.backgroundColor } },
                                scales: {
                                    x: { title: { display: true, text: 'Tanggal' }, grid: { display: false }, border: { color: cardStyle.borderColor }, ticks: { maxTicksLimit: 7, color: textStyle.color } },
                                    y: { beginAtZero: true, grid: { color: cardStyle.borderColor }, border: { color: cardStyle.borderColor }, ticks: { color: textStyle.color }, title: { display: true, text: 'Skor akhir' } }
                                }
                            }
                        });
                        document.getElementById('trend-fallback').hidden = true;
                        document.getElementById('trend-data').open = false;
                    } catch (error) {
                        container.hidden = true;
                    }
                })();
            </script>
        @else
            <p class="text-sm text-gray-600">Belum ada data tren skor untuk ditampilkan.</p>
        @endif
    </section>
        <section class="ews-crud">
          
            @if ($rekomendasi)
                <div class="grid gap-6 md:grid-cols-2">
                    @foreach (['penyebab' => 'Penyebab', 'saran' => 'Saran pendampingan'] as $key => $label)
                        <div>
                            <h3 class="mb-3 text-sm font-semibold">{{ $label }}</h3>
                            <ul class="list-disc space-y-2 pl-5 text-sm leading-7">
                                @forelse (($rekomendasi->$key ?? []) as $item)
                                    <li class="break-words">{{ $item }}</li>
                                @empty
                                    <li>Belum ada {{ strtolower($label) }} untuk ditampilkan.</li>
                                @endforelse
                            </ul>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-600">Belum ada rekomendasi AI untuk siswa ini.</p>
            @endif
        </section>
        <section class="ews-crud">
            <h2 class="ews-card-title">Masukan untuk rekomendasi</h2>
            <form method="POST" action="{{ route('guru_bk.monitoring.feedback.store', [$kelas, $siswa]) }}">
                @csrf
                <div class="ews-fields">
                    <div>
                        <label for="catatan" class="block">Catatan</label>
                        <textarea id="catatan" name="catatan" rows="4" required aria-invalid="{{ $errors->has('catatan') ? 'true' : 'false' }}" aria-describedby="catatan-hint{{ $errors->has('catatan') ? ' catatan-error' : '' }}" @disabled(!$rekomendasi)>{{ old('catatan') }}</textarea>
                        <p id="catatan-hint" class="ews-field-hint">Rekomendasi akan diperbarui otomatis dalam beberapa menit setelah masukan dikirim.</p>
                        @error('catatan')
                            <p id="catatan-error" class="mt-2 text-sm text-red-700" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                @if (!$rekomendasi)<p class="mt-4 text-sm text-gray-600">Masukan dapat dikirim setelah rekomendasi pertama tersedia.</p>@endif
                <div class="ews-form-actions"><button class="ews-button ews-button-blue" type="submit" @disabled(!$rekomendasi)>Kirim Masukan untuk Perbaiki Rekomendasi</button></div>
            </form>
        </section>
        <section class="ews-crud">
            <h2 class="ews-card-title">Riwayat perilaku</h2>
            <p class="ews-table-hint md:hidden">Geser tabel ke samping untuk melihat seluruh kolom.</p>
            <div class="ews-table-scroll" role="region" aria-label="Riwayat perilaku" tabindex="0">
                <table>
                    <thead><tr><th scope="col">Tanggal</th><th scope="col">Perilaku</th><th scope="col">Jenis</th><th scope="col">Catatan</th></tr></thead>
                    <tbody>
                        @forelse ($riwayatPerilaku as $catatan)
                            <tr>
                                <td class="whitespace-nowrap">{{ $catatan->tanggal->format('d M Y') }}</td>
                                <td>{{ $catatan->perilaku?->nama ?? '-' }}</td>
                                <td><span class="inline-flex rounded px-2.5 py-1 text-xs font-medium {{ $catatan->perilaku?->jenis === 'positif' ? 'bg-green-100 text-green-700' : ($catatan->perilaku?->jenis === 'negatif' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700') }}">{{ ucfirst($catatan->perilaku?->jenis ?? 'Belum ada data') }}</span></td>
                                <td class="min-w-48 whitespace-pre-wrap break-words">{{ $catatan->catatan ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4">Belum ada riwayat perilaku untuk ditampilkan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
