@extends('layouts.siswa')

@section('title', 'Dashboard Siswa')

@section('siswa-content')
    @php
        $kategoriStyles = [
            'aman' => 'bg-green-100 text-green-700',
            'perhatian' => 'bg-yellow-100 text-yellow-700',
            'binaan' => 'bg-red-100 text-red-700',
        ];
        $kategoriLabels = ['aman' => 'Aman', 'perhatian' => 'Perhatian', 'binaan' => 'Binaan'];
    @endphp
    @php
        $hasil = $hasilTerkini ?? null;
        $trend = $trend ?? collect();
        $rekomendasi = $rekomendasi ?? null;
        $pesan = match ($hasil?->kategori) {
            'aman' => 'Pertahankan kebiasaan baikmu. Setiap langkah kecil membantu kamu terus berkembang.',
            'perhatian', 'binaan' => 'Ada beberapa hal yang bisa diperbaiki. Kamu bisa mulai dari satu langkah kecil dan meminta dukungan guru.',
            default => 'Belum ada ringkasan perkembangan saat ini. Tetap semangat menjalani kegiatan belajarmu.',
        };
    @endphp
    <div class="space-y-6">
        <section class="ews-crud">
            <h1>Dashboard Siswa</h1>
            <p>Selamat datang, {{ auth()->user()->name }}.</p>
        </section>
        <section class="ews-crud">
            <h2 class="ews-card-title">Perkembangan kamu</h2>
            <div class="flex flex-wrap items-center gap-4">
                <div><p class="text-sm text-gray-600">Skor terkini</p><p class="mt-2 text-4xl font-semibold tabular-nums">{{ $hasil?->skor_akhir === null ? '-' : number_format((float) $hasil->skor_akhir, 2, ',', '.') }}</p></div>
                <span class="inline-flex rounded px-2.5 py-1 text-xs font-medium {{ $kategoriStyles[$hasil?->kategori] ?? 'bg-gray-100 text-gray-700' }}">{{ $kategoriLabels[$hasil?->kategori] ?? 'Belum ada data' }}</span>
            </div>
            <p class="mt-4 text-sm leading-7">{{ $pesan }}</p>
            @if ($hasil?->data_tidak_lengkap)
                <p class="mt-4 rounded bg-yellow-100 p-4 text-sm text-yellow-700">Sebagian data masih dilengkapi. Ringkasan ini dapat berubah setelah data diperbarui.</p>
            @endif
        </section>
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
            <h2 class="ews-card-title">Refleksi untuk Kamu</h2>
            @if ($rekomendasi && count($rekomendasi->saran ?? []))
                <ul class="list-disc space-y-3 pl-5 text-sm leading-7">
                    @foreach ($rekomendasi->saran as $saran)
                        <li class="break-words">{{ $saran }}</li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-gray-600">Belum ada refleksi untuk ditampilkan saat ini.</p>
            @endif
        </section>
    </div>
@endsection
