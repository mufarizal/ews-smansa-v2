@extends('layouts.guru_bk')

@section('title', 'Monitoring Siswa')

@section('guru-bk-content')
    @php
        $kategoriStyles = [
            'aman' => 'bg-green-100 text-green-700',
            'perhatian' => 'bg-yellow-100 text-yellow-700',
            'binaan' => 'bg-red-100 text-red-700',
        ];
        $kategoriLabels = ['aman' => 'Aman', 'perhatian' => 'Perhatian', 'binaan' => 'Binaan'];
    @endphp
    <div class="space-y-6">
        <section class="ews-crud">
            <h1>Monitoring Siswa</h1>
            <p>Pantau ringkasan kondisi siswa dan tentukan pendampingan yang dibutuhkan.</p>
        </section>
        <div class="grid gap-6 lg:grid-cols-2">
            @forelse ($kelasList as $kelas)
                @php
                    $ringkasan = $ringkasanPerKelas[$kelas->id] ?? [];
                @endphp
                <section class="ews-crud">
                    <h2 class="ews-card-title">{{ $kelas->nama }}</h2>
                    <dl class="grid grid-cols-2 gap-4">
                        @foreach (['aman' => 'Aman', 'perhatian' => 'Perhatian', 'binaan' => 'Binaan', 'belum_ada_data' => 'Belum ada data'] as $key => $label)
                            <div class="rounded p-4 {{ $kategoriStyles[$key] ?? 'bg-gray-100 text-gray-700' }}">
                                <dt class="text-sm">{{ $label }}</dt>
                                <dd class="mt-2 text-2xl font-semibold tabular-nums">{{ $ringkasan[$key] ?? 0 }}</dd>
                            </div>
                        @endforeach
                    </dl>
                    <div class="ews-form-actions">
                        <a class="ews-button ews-button-blue" href="{{ route('guru_bk.monitoring.show', $kelas) }}" aria-label="Lihat detail kelas {{ $kelas->nama }}">Lihat Detail</a>
                    </div>
                </section>
            @empty
                <div class="ews-crud lg:col-span-2"><p class="text-sm text-gray-600">Belum ada kelas yang ditugaskan kepada Anda.</p></div>
            @endforelse
        </div>
    </div>
@endsection
