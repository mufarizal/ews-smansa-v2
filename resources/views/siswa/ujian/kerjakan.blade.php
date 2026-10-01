@extends('layouts.siswa')

@section('title', 'Kerjakan Ujian')

@section('siswa-content')
    @if ($hasil->status !== 'sedang_mengerjakan')
        <div class="ews-crud">
            <h1>{{ $ujian->judul }}</h1>
            <p>Sesi pengerjaan tidak aktif. Periksa status ujian pada halaman hasil.</p>
            <a href="{{ route('siswa.ujian.selesai', $ujian) }}" class="ews-button ews-button-secondary">Lihat Hasil</a>
        </div>
    @else
        @php
            $sisaDetik = max(0, (int) ceil($hasil->sisaDetik()));
            $jumlahTersimpan = $soalList->filter(fn ($soal) => $jawabanList->has($soal->id))->count();
        @endphp
        <div id="exam-work" class="space-y-5" data-exam-seconds="{{ $sisaDetik }}">
            <div id="exam-timer" class="sticky top-2 z-20 flex flex-wrap items-center justify-between gap-3 rounded-lg border p-4 shadow-sm {{ $sisaDetik < 300 ? 'border-red-300 bg-red-50 text-red-900' : 'border-green-200 bg-green-50 text-green-900' }}">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide">Sisa waktu ujian</p>
                    <p id="exam-timer-message" class="mt-1 text-xs" role="status">{{ $sisaDetik < 300 ? 'Waktu hampir habis. Simpan jawaban Anda.' : 'Timer terus berjalan selama ujian.' }}</p>
                </div>
                <output id="exam-countdown" role="timer" aria-label="Sisa waktu ujian" aria-live="off" class="text-3xl font-bold tabular-nums">{{ sprintf('%02d:%02d', intdiv($sisaDetik, 60), $sisaDetik % 60) }}</output>
            </div>

            <header class="ews-crud">
                <p class="ews-eyebrow">Ujian Harian · {{ $ujian->durasi_menit }} menit</p>
                <h1>{{ $ujian->judul }}</h1>
                @if ($ujian->deskripsi)
                    <div class="mb-5 whitespace-pre-wrap break-words text-sm leading-7 text-gray-600">{{ $ujian->deskripsi }}</div>
                @endif
                <p class="text-sm text-gray-600">{{ $soalList->count() }} soal · {{ $jumlahTersimpan }} jawaban tersimpan</p>
                <div class="mt-4 rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm leading-6 text-gray-600">
                    Simpan jawaban pada setiap soal. Saat waktu habis, ujian dikumpulkan otomatis dengan jawaban yang sudah tersimpan.
                </div>
                <noscript><p class="ews-notice ews-notice-error mt-4">Aktifkan JavaScript agar timer dan pengumpulan otomatis dapat berjalan. Waktu ujian tetap berjalan di server.</p></noscript>
                @if ($errors->any())
                    <div class="ews-notice ews-notice-error mt-4" role="alert">
                        <strong>Jawaban belum tersimpan.</strong>
                        <ul class="mt-2 list-disc pl-5">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
                    </div>
                @endif
            </header>

            @forelse ($soalList as $soal)
                @php
                    $jawaban = $jawabanList->get($soal->id);
                    $formAktif = (string) old('_soal_id') === (string) $soal->id;
                    $jawabanTeks = $formAktif ? old('jawaban_teks', $jawaban?->jawaban_teks) : $jawaban?->jawaban_teks;
                    $jawabanError = $formAktif && $errors->has('jawaban_teks');
                @endphp
                <section class="ews-crud scroll-mt-28" id="soal-{{ $soal->id }}" aria-labelledby="soal-heading-{{ $soal->id }}">
                    <div class="mb-5 flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 pb-4">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 id="soal-heading-{{ $soal->id }}" class="font-semibold">Soal {{ $soal->urutan ?? $loop->iteration }}</h2>
                            <span class="rounded bg-gray-100 px-2.5 py-1 text-xs text-gray-600">{{ $soal->tipe_soal === 'pilihan_ganda' ? 'Pilihan Ganda' : 'Esai' }}</span>
                        </div>
                        <span class="text-xs font-medium text-gray-500">{{ $soal->poin }} poin</span>
                    </div>
                    <p class="mb-5 whitespace-pre-wrap break-words text-sm leading-7">{{ $soal->pertanyaan }}</p>
                    <form action="{{ route('siswa.ujian.jawab.store', [$ujian, $soal]) }}" method="POST" data-exam-answer-form>
                        @csrf
                        <input type="hidden" name="_soal_id" value="{{ $soal->id }}">
                        @if ($soal->tipe_soal === 'pilihan_ganda')
                            <fieldset @if ($jawabanError) aria-describedby="jawaban-error-{{ $soal->id }}" @endif>
                                <legend class="mb-3 text-sm font-semibold">Pilih satu jawaban</legend>
                                <div class="grid gap-2">
                                    @foreach (['A', 'B', 'C', 'D'] as $opsi)
                                        <label for="jawaban-{{ $soal->id }}-{{ $opsi }}" class="flex items-start">
                                            <input type="radio" id="jawaban-{{ $soal->id }}-{{ $opsi }}" name="jawaban_teks" value="{{ $opsi }}" required class="mt-0.5 shrink-0"
                                                @checked(strtoupper($jawabanTeks ?? '') === $opsi) @if ($jawabanError) aria-invalid="true" @endif>
                                            <span class="min-w-0 break-words"><strong>{{ $opsi }}.</strong> {{ $soal->{'opsi_' . strtolower($opsi)} }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>
                        @else
                            <label for="jawaban-{{ $soal->id }}" class="block">Jawaban Anda</label>
                            <textarea id="jawaban-{{ $soal->id }}" name="jawaban_teks" rows="5" class="w-full" required placeholder="Tulis jawaban Anda di sini."
                                @if ($jawabanError) aria-invalid="true" aria-describedby="jawaban-error-{{ $soal->id }}" @endif>{{ $jawabanTeks }}</textarea>
                        @endif
                        @if ($jawabanError)
                            <p id="jawaban-error-{{ $soal->id }}" class="mt-2 text-sm text-red-700">{{ $errors->first('jawaban_teks') }}</p>
                        @endif
                        <div class="ews-form-actions">
                            <button type="submit" class="ews-button ews-button-secondary" aria-label="Simpan jawaban soal {{ $soal->urutan ?? $loop->iteration }}">Simpan Jawaban</button>
                            <p class="text-xs {{ $jawaban ? 'text-green-700' : 'text-gray-500' }}">{{ $jawaban ? 'Jawaban tersimpan. Simpan lagi setelah mengubahnya.' : 'Jawaban belum disimpan.' }}</p>
                        </div>
                    </form>
                </section>
            @empty
                <div class="ews-crud text-center"><p>Belum ada soal pada ujian ini.</p></div>
            @endforelse

            <section class="ews-crud">
                <h2 class="text-lg font-semibold">Selesaikan ujian</h2>
                <p class="mt-2 text-sm text-gray-600">Pastikan semua jawaban telah disimpan. Jawaban tidak dapat diubah setelah ujian dikumpulkan.</p>
                <form id="exam-submit-form" action="{{ route('siswa.ujian.submit', $ujian) }}" method="POST" class="ews-form-actions"
                    onsubmit="return confirm('Yakin ingin mengumpulkan ujian? Jawaban tidak dapat diubah setelah ini.')">
                    @csrf
                    <button type="submit" class="ews-button ews-button-primary w-full sm:w-auto">Selesai &amp; Kumpulkan Ujian</button>
                </form>
            </section>
            <form id="exam-auto-submit-form" action="{{ route('siswa.ujian.submit', $ujian) }}" method="POST" hidden>
                @csrf
            </form>
        </div>

        <script>
            (() => {
                const work = document.getElementById('exam-work');
                const initialSeconds = Number(work.dataset.examSeconds);
                const timer = document.getElementById('exam-timer');
                const countdown = document.getElementById('exam-countdown');
                const message = document.getElementById('exam-timer-message');
                const autoForm = document.getElementById('exam-auto-submit-form');
                const manualForm = document.getElementById('exam-submit-form');
                const deadline = Date.now() + initialSeconds * 1000;
                let submitted = false;
                let interval;
                let warned = initialSeconds < 300;
                const remaining = () => Math.max(0, Math.ceil((deadline - Date.now()) / 1000));

                function finishAutomatically() {
                    if (submitted) return;
                    submitted = true;
                    clearInterval(interval);
                    message.textContent = 'Waktu habis. Ujian sedang dikumpulkan…';
                    work.querySelectorAll('button[type="submit"]').forEach(button => { button.disabled = true; });
                    autoForm.submit();
                }

                function tick() {
                    const seconds = remaining();
                    countdown.textContent = String(Math.floor(seconds / 60)).padStart(2, '0') + ':' + String(seconds % 60).padStart(2, '0');
                    const urgent = seconds < 300;
                    ['border-red-300', 'bg-red-50', 'text-red-900'].forEach(name => timer.classList.toggle(name, urgent));
                    ['border-green-200', 'bg-green-50', 'text-green-900'].forEach(name => timer.classList.toggle(name, !urgent));
                    if (urgent && !warned) {
                        warned = true;
                        message.textContent = 'Waktu kurang dari 5 menit. Simpan jawaban Anda.';
                    }
                    if (seconds === 0) finishAutomatically();
                }

                // Wall-clock deadline keeps the countdown accurate after tab throttling or a confirmation dialog.
                interval = setInterval(tick, 1000);
                tick();
                document.addEventListener('visibilitychange', tick);
                window.addEventListener('pageshow', event => {
                    if (event.persisted) window.location.reload();
                });
                // Replace the native fallback confirmation so expiry can bypass it safely.
                manualForm.removeAttribute('onsubmit');
                manualForm.addEventListener('submit', event => {
                    if (submitted) { event.preventDefault(); return; }
                    if (remaining() === 0) { event.preventDefault(); tick(); return; }
                    if (!window.confirm('Yakin ingin mengumpulkan ujian? Jawaban tidak dapat diubah setelah ini.')) {
                        event.preventDefault();
                        tick();
                        return;
                    }
                    if (remaining() === 0) { event.preventDefault(); tick(); return; }
                    submitted = true;
                    clearInterval(interval);
                });
                work.querySelectorAll('[data-exam-answer-form]').forEach(form => {
                    form.addEventListener('submit', event => {
                        if (submitted) event.preventDefault();
                        else if (remaining() === 0) { event.preventDefault(); tick(); }
                    });
                });
            })();
        </script>
    @endif
@endsection
