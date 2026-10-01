@extends('layouts.siswa')

@section('title', $tugas->judul)

@section('siswa-content')
    @php
        $status = $nilaiTugas?->status ?? 'belum_dikerjakan';
        $readOnly = in_array($status, ['menunggu_penilaian', 'selesai'], true);
        $statusLabels = [
            'belum_dikerjakan' => 'Belum dikerjakan',
            'sedang_dikerjakan' => 'Sedang dikerjakan',
            'menunggu_penilaian' => 'Menunggu penilaian',
            'selesai' => 'Selesai',
        ];
        $statusColors = [
            'belum_dikerjakan' => 'border-gray-200 bg-gray-100 text-gray-700',
            'sedang_dikerjakan' => 'border-yellow-200 bg-yellow-50 text-yellow-800',
            'menunggu_penilaian' => 'border-orange-200 bg-orange-50 text-orange-800',
            'selesai' => 'border-green-200 bg-green-50 text-green-800',
        ];
        $tipeLabels = ['pilihan_ganda' => 'Pilihan Ganda', 'esai' => 'Esai', 'upload_file' => 'Upload File'];
        $sekarang = now();
        $deadline = $tugas->tanggal_selesai->copy();
        $deadlineLewat = $sekarang->gt($deadline);
        $sisaWaktu = $deadlineLewat
            ? 'Batas waktu sudah berakhir'
            : 'Sisa waktu: ' . $deadline->locale('id')->diffForHumans($sekarang, true, false, 2);
        $jumlahTersimpan = $soalList->filter(fn ($soal) => $jawabanList->has($soal->id))->count();
    @endphp

    <div class="space-y-5">
        <div class="ews-crud">
            <a href="{{ route('siswa.tugas.index') }}" class="ews-back-link">← Kembali ke tugas saya</a>
            <div class="mb-4 flex flex-wrap items-center gap-2">
                <span class="inline-flex rounded-md border px-2.5 py-1 text-xs font-medium {{ $statusColors[$status] ?? $statusColors['belum_dikerjakan'] }}">
                    {{ $statusLabels[$status] ?? 'Belum dikerjakan' }}
                </span>
                @if (! $readOnly)
                    <span class="inline-flex rounded-md border px-2.5 py-1 text-xs font-medium {{ $deadlineLewat ? 'border-red-200 bg-red-50 text-red-800' : 'border-gray-200 bg-gray-50 text-gray-700' }}">{{ $sisaWaktu }}</span>
                @endif
            </div>
            <h1>{{ $tugas->judul }}</h1>
            @if ($tugas->deskripsi)
                <div class="mb-5 whitespace-pre-wrap break-words text-sm leading-relaxed text-gray-600">{{ $tugas->deskripsi }}</div>
            @endif
            <dl class="grid gap-4 border-t border-gray-200 pt-5 text-sm sm:grid-cols-3">
                <div>
                    <dt class="text-xs text-gray-500">Periode tugas</dt>
                    <dd class="mt-1 font-medium">{{ $tugas->tanggal_mulai->translatedFormat('d M Y') }} – {{ $deadline->translatedFormat('d M Y') }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">Batas pengumpulan</dt>
                    <dd class="mt-1 font-medium"><time datetime="{{ $deadline->toIso8601String() }}">{{ $deadline->translatedFormat('d M Y, H.i') }}</time></dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">Jawaban tersimpan</dt>
                    <dd class="mt-1 font-medium">{{ $jumlahTersimpan }} dari {{ $soalList->count() }} soal</dd>
                </div>
            </dl>

            @if ($readOnly)
                <div class="mt-5 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-900" role="status">
                    @if ($status === 'selesai')
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="font-semibold">Tugas selesai dinilai</p>
                                <p class="mt-1 text-green-800">Jawaban yang sudah dikumpulkan dapat dilihat di bawah.</p>
                            </div>
                            <div class="rounded-md border border-green-200 bg-white px-5 py-3 text-center">
                                <span class="block text-xs text-green-800">Nilai akhir</span>
                                <strong class="mt-1 block text-2xl">{{ $nilaiTugas->nilai ?? '-' }}</strong>
                            </div>
                        </div>
                    @else
                        <p class="font-semibold">Tugas sudah dikumpulkan</p>
                        <p class="mt-1">Jawaban tidak dapat diubah. Hasil akan tampil setelah guru selesai menilai.</p>
                    @endif
                </div>
            @else
                <div class="mt-5 rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm text-gray-600">
                    <p class="font-semibold text-gray-800">Simpan setiap jawaban sebelum mengumpulkan</p>
                    <p class="mt-1">Gunakan tombol Simpan Jawaban pada masing-masing soal. Setelah semuanya siap, pilih Kumpulkan Tugas di bagian bawah.</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="ews-notice ews-notice-error mt-5" role="alert">
                    <strong>Jawaban belum berhasil disimpan.</strong>
                    <ul class="mt-2 list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        @forelse ($soalList as $soal)
            @php
                $jawaban = $jawabanList->get($soal->id);
                $formAktif = (string) old('_soal_id') === (string) $soal->id;
                $jawabanTersimpan = $jawaban?->jawaban_teks ?? $jawaban?->jawaban_text;
                $jawabanTeks = $formAktif ? old('jawaban_teks', $jawabanTersimpan) : $jawabanTersimpan;
                $jawabanPilihan = strtolower((string) ($readOnly ? $jawabanTersimpan : $jawabanTeks));
                $jawabanError = $formAktif && $errors->has('jawaban_teks');
                $fileError = $formAktif && $errors->has('file');
            @endphp
            <section class="ews-crud" id="soal-{{ $soal->id }}" aria-labelledby="judul-soal-{{ $soal->id }}">
                <div class="mb-5 flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 pb-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <h2 id="judul-soal-{{ $soal->id }}" class="font-semibold text-gray-900">Soal {{ $loop->iteration }}</h2>
                        <span class="rounded-md border border-gray-200 bg-gray-50 px-2.5 py-1 text-xs text-gray-600">{{ $tipeLabels[$soal->tipe_soal] ?? $soal->tipe_soal }}</span>
                    </div>
                    <span class="text-xs font-medium text-gray-500">{{ $soal->poin }} poin</span>
                </div>
                <p class="mb-5 whitespace-pre-wrap break-words text-sm leading-relaxed text-gray-800">{{ $soal->pertanyaan }}</p>

                @if ($readOnly)
                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <p class="mb-2 text-xs font-semibold text-gray-500">Jawaban kamu</p>
                        @if ($soal->tipe_soal === 'pilihan_ganda')
                            @if (in_array($jawabanPilihan, ['a', 'b', 'c', 'd'], true))
                                <p class="break-words text-sm text-gray-800"><strong>{{ strtoupper($jawabanPilihan) }}.</strong> {{ $soal->{'opsi_' . $jawabanPilihan} }}</p>
                            @else
                                <p class="text-sm text-gray-500">Belum ada jawaban.</p>
                            @endif
                        @elseif ($soal->tipe_soal === 'upload_file')
                            @if ($jawaban?->file_path)
                                <a href="{{ asset('storage/' . $jawaban->file_path) }}" target="_blank" rel="noopener noreferrer"
                                    class="break-all text-sm font-medium text-[#365e3b] underline underline-offset-4">{{ basename($jawaban->file_path) }}</a>
                                <p class="mt-1 text-xs text-gray-500">Buka file jawaban di tab baru.</p>
                            @else
                                <p class="text-sm text-gray-500">Belum ada file yang diunggah.</p>
                            @endif
                        @else
                            <blockquote class="whitespace-pre-wrap break-words text-sm leading-relaxed text-gray-800">{{ $jawabanTersimpan ?? 'Belum ada jawaban.' }}</blockquote>
                        @endif
                    </div>
                @else
                    <form action="{{ route('siswa.tugas.jawab.store', [$tugas, $soal]) }}" method="POST"
                        @if ($soal->tipe_soal === 'upload_file') enctype="multipart/form-data" @endif>
                        @csrf
                        <input type="hidden" name="_soal_id" value="{{ $soal->id }}">

                        @if ($soal->tipe_soal === 'pilihan_ganda')
                            <fieldset @if ($jawabanError) aria-describedby="jawaban-error-{{ $soal->id }}" @endif>
                                <legend class="mb-3 text-sm font-semibold text-gray-700">Pilih satu jawaban <span class="text-red-700">*</span></legend>
                                <div class="grid gap-2">
                                    @foreach (['a', 'b', 'c', 'd'] as $opsi)
                                        <label for="jawaban-{{ $soal->id }}-{{ $opsi }}" class="flex items-start hover:border-[#9db58f]">
                                            <input type="radio" id="jawaban-{{ $soal->id }}-{{ $opsi }}" name="jawaban_teks"
                                                value="{{ $opsi }}" class="mt-0.5 shrink-0" required
                                                @checked($jawabanPilihan === $opsi) @if ($jawabanError) aria-invalid="true" @endif>
                                            <span class="min-w-0 break-words"><strong>{{ strtoupper($opsi) }}.</strong> {{ $soal->{'opsi_' . $opsi} }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>
                        @elseif ($soal->tipe_soal === 'esai')
                            <label for="jawaban-{{ $soal->id }}" class="block">Jawaban kamu <span class="text-red-700">*</span></label>
                            <textarea id="jawaban-{{ $soal->id }}" name="jawaban_teks" rows="5" class="w-full" required
                                placeholder="Tulis jawabanmu di sini..."
                                @if ($jawabanError) aria-invalid="true" aria-describedby="jawaban-error-{{ $soal->id }}" @endif>{{ $jawabanTeks }}</textarea>
                        @elseif ($soal->tipe_soal === 'upload_file')
                            @if ($jawaban?->file_path)
                                <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm">
                                    <p class="mb-1 text-xs font-medium text-green-800">File tersimpan</p>
                                    <a href="{{ asset('storage/' . $jawaban->file_path) }}" target="_blank" rel="noopener noreferrer"
                                        class="break-all font-medium text-green-900 underline underline-offset-4">{{ basename($jawaban->file_path) }}</a>
                                </div>
                            @endif
                            <label for="file-{{ $soal->id }}" class="block">{{ $jawaban?->file_path ? 'Ganti file jawaban' : 'File jawaban' }} <span class="text-red-700">*</span></label>
                            <input type="file" id="file-{{ $soal->id }}" name="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required
                                aria-describedby="file-hint-{{ $soal->id }}{{ $fileError ? ' file-error-' . $soal->id : '' }}"
                                @if ($fileError) aria-invalid="true" @endif>
                            <p id="file-hint-{{ $soal->id }}" class="ews-field-hint">PDF, JPG, PNG, DOC, atau DOCX. Maksimal 5 MB. {{ $jawaban?->file_path ? 'Pilih file baru hanya jika ingin mengganti jawaban.' : 'Pilih satu file, lalu simpan jawaban.' }}</p>
                        @endif

                        @if ($jawabanError)
                            <p id="jawaban-error-{{ $soal->id }}" class="mt-2 text-sm text-red-700">{{ $errors->first('jawaban_teks') }}</p>
                        @endif
                        @if ($fileError)
                            <p id="file-error-{{ $soal->id }}" class="mt-2 text-sm text-red-700">{{ $errors->first('file') }}</p>
                        @endif

                        <div class="ews-form-actions">
                            <button type="submit" class="ews-button ews-button-secondary" aria-label="Simpan jawaban soal {{ $loop->iteration }}">Simpan Jawaban</button>
                            <p class="text-xs {{ $jawaban ? 'text-green-700' : 'text-gray-500' }}">{{ $jawaban ? 'Jawaban sudah pernah disimpan. Simpan lagi jika ada perubahan.' : 'Jawaban belum disimpan.' }}</p>
                        </div>
                    </form>
                @endif
            </section>
        @empty
            <div class="ews-crud py-10 text-center">
                <h2 class="font-semibold text-gray-800">Belum ada soal</h2>
                <p class="mt-2 text-sm text-gray-600">Soal tugas ini belum tersedia. Hubungi guru mata pelajaran untuk informasi lebih lanjut.</p>
            </div>
        @endforelse

        @if (! $readOnly)
            <div class="ews-crud">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <h2 class="text-lg font-semibold text-gray-900">Siap mengumpulkan tugas?</h2>
                        <p class="mt-2 text-sm text-gray-600">{{ $jumlahTersimpan }} dari {{ $soalList->count() }} jawaban tersimpan. Periksa semua jawaban sebelum mengumpulkan.</p>
                        <p class="mt-1 text-xs text-gray-500">Jawaban tidak dapat diubah setelah tugas dikumpulkan.</p>
                    </div>
                    <form action="{{ route('siswa.tugas.submit', $tugas) }}" method="POST" class="shrink-0"
                        onsubmit="return confirm('Yakin ingin mengumpulkan? Jawaban tidak bisa diubah lagi setelah ini.')">
                        @csrf
                        <button type="submit" class="ews-button ews-button-primary w-full sm:w-auto">Kumpulkan Tugas</button>
                    </form>
                </div>
            </div>
        @endif
    </div>
@endsection
