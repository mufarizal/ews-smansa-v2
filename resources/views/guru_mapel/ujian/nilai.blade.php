@extends('layouts.guru_mapel')

@section('title', 'Penilaian Ujian')

@section('guru-mapel-content')
    @php
        $hasManualQuestions = $soalList->contains(fn ($soal) => $soal->tipe_soal === 'esai');
        $typeLabels = ['pilihan_ganda' => 'Pilihan Ganda', 'esai' => 'Esai'];
    @endphp

    <div class="ews-crud ews-crud-form">
        <a href="{{ route('guru_mapel.ujian.hasil', $ujian) }}" class="ews-back-link">← Kembali ke hasil ujian</a>
        <h1>Penilaian Ujian</h1>
        <p class="break-words">{{ $ujian->judul }}</p>

        <div class="mb-6 flex flex-wrap items-center justify-between gap-4 rounded-lg border border-gray-200 bg-gray-50 p-4 sm:p-5">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Jawaban siswa</p>
                <h2 class="mt-1 break-words text-lg font-semibold text-gray-800">{{ $siswa->nama }}</h2>
                <p class="mt-1 text-sm text-gray-600">NIS {{ $siswa->nis }}</p>
            </div>
            <span class="rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-gray-600">{{ $soalList->count() }} soal · {{ $soalList->sum('poin') }} poin maksimal</span>
        </div>

        @if ($errors->any())
            <div class="ews-notice ews-notice-error" role="alert">
                <strong>Penilaian belum tersimpan. Periksa kembali poin yang diisi.</strong>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($hasManualQuestions)
            <p class="mb-5 text-sm leading-6 text-gray-600">Nilai pilihan ganda dihitung otomatis. Isi poin untuk jawaban esai sesuai batas maksimal setiap soal. Poin kosong akan disimpan sebagai 0.</p>
            <form action="{{ route('guru_mapel.ujian.nilai.store', [$ujian, $siswa]) }}" method="POST">
                @csrf
        @else
            <div>
        @endif

            <div class="space-y-5">
                @forelse ($soalList as $soal)
                    @php
                        $jawaban = $jawabanList->get($soal->id);
                        $jawabanTeks = $jawaban?->jawaban_teks;
                        $isMultipleChoice = $soal->tipe_soal === 'pilihan_ganda';
                        $isManualQuestion = $soal->tipe_soal === 'esai';
                        $pilihanSiswa = strtolower($jawabanTeks ?? '');
                        $kunciJawaban = strtolower($soal->kunci_jawaban ?? '');
                    @endphp

                    <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm" aria-labelledby="soal-heading-{{ $soal->id }}">
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 bg-gray-50 px-4 py-3 sm:px-5">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 id="soal-heading-{{ $soal->id }}" class="text-sm font-semibold text-gray-800">Soal {{ $soal->urutan ?? $loop->iteration }}</h2>
                                <span class="rounded-full bg-gray-200 px-2.5 py-1 text-xs font-medium text-gray-700">{{ $typeLabels[$soal->tipe_soal] ?? $soal->tipe_soal }}</span>
                            </div>
                            <span class="text-xs font-semibold text-gray-600">Maks. {{ $soal->poin }} poin</span>
                        </div>

                        <div class="space-y-5 p-4 sm:p-5">
                            <p class="whitespace-pre-wrap break-words text-sm leading-7 text-gray-800">{{ $soal->pertanyaan }}</p>

                            @if ($isMultipleChoice)
                                <dl class="grid gap-3 sm:grid-cols-2">
                                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                                        <dt class="text-xs font-semibold text-gray-500">Jawaban siswa</dt>
                                        <dd class="mt-2 break-words text-sm leading-6 text-gray-800">
                                            @if (filled($jawabanTeks))
                                                <strong>{{ strtoupper($jawabanTeks) }}</strong>
                                                @if (in_array($pilihanSiswa, ['a', 'b', 'c', 'd'], true))
                                                    <span> — {{ $soal->{'opsi_' . $pilihanSiswa} }}</span>
                                                @endif
                                            @else
                                                Belum dijawab
                                            @endif
                                        </dd>
                                    </div>
                                    <div class="rounded-lg border border-green-200 bg-green-50 p-4">
                                        <dt class="text-xs font-semibold text-green-700">Kunci jawaban</dt>
                                        <dd class="mt-2 break-words text-sm leading-6 text-green-900">
                                            <strong>{{ strtoupper($soal->kunci_jawaban ?? '-') }}</strong>
                                            @if (in_array($kunciJawaban, ['a', 'b', 'c', 'd'], true))
                                                <span> — {{ $soal->{'opsi_' . $kunciJawaban} }}</span>
                                            @endif
                                        </dd>
                                    </div>
                                </dl>
                                <p class="text-sm text-gray-600">Poin otomatis: <strong class="text-gray-900">{{ $jawaban?->poin_diperoleh ?? '-' }} / {{ $soal->poin }}</strong></p>
                            @elseif ($soal->tipe_soal === 'esai')
                                <div>
                                    <h3 class="mb-2 text-xs font-semibold text-gray-500">Jawaban siswa</h3>
                                    <blockquote class="whitespace-pre-wrap break-words rounded-lg border-l-4 border-gray-300 bg-gray-50 p-4 text-sm leading-7 text-gray-800">{{ filled($jawabanTeks) ? $jawabanTeks : 'Belum ada jawaban.' }}</blockquote>
                                </div>
                            @endif

                            @if ($isManualQuestion)
                                <div class="border-t border-gray-100 pt-4">
                                    <label for="poin-{{ $soal->id }}" class="block">Poin soal {{ $soal->urutan ?? $loop->iteration }}</label>
                                    <div class="flex items-center gap-3">
                                        <input type="number" id="poin-{{ $soal->id }}" name="poin[{{ $soal->id }}]"
                                            min="0" max="{{ $soal->poin ?? 0 }}" step="1"
                                            value="{{ old('poin.' . $soal->id, $jawaban?->poin_diperoleh) }}"
                                            class="w-28" placeholder="0" aria-describedby="poin-hint-{{ $soal->id }}{{ $errors->has('poin.' . $soal->id) ? ' poin-error-' . $soal->id : '' }}"
                                            aria-invalid="{{ $errors->has('poin.' . $soal->id) ? 'true' : 'false' }}">
                                        <span class="text-sm text-gray-500">/ {{ $soal->poin }} poin</span>
                                    </div>
                                    <p id="poin-hint-{{ $soal->id }}" class="ews-field-hint">Isi bilangan bulat antara 0 dan {{ $soal->poin }}.</p>
                                    @error('poin.' . $soal->id)
                                        <p id="poin-error-{{ $soal->id }}" class="mt-2 text-sm text-red-700">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </section>
                @empty
                    <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 px-5 py-10 text-center">
                        <h2 class="text-base font-semibold text-gray-800">Belum ada soal</h2>
                        <p class="mt-2 text-sm text-gray-600">Ujian ini belum memiliki soal untuk dinilai.</p>
                    </div>
                @endforelse
            </div>

            @if ($hasManualQuestions)
                <div class="ews-form-actions">
                    <button type="submit" class="ews-button ews-button-primary">Simpan penilaian</button>
                    <a href="{{ route('guru_mapel.ujian.hasil', $ujian) }}" class="ews-button ews-button-secondary">Kembali</a>
                </div>
            </form>
        @else
                @if ($soalList->isNotEmpty())
                    <div class="mt-5 rounded-lg border border-green-200 bg-green-50 p-4 text-sm leading-6 text-green-900">
                        Semua soal pilihan ganda dinilai otomatis. Tidak ada poin yang perlu diisi secara manual.
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection
