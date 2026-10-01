@extends('layouts.guru_mapel')

@section('title', 'Detail Tugas')

@section('guru-mapel-content')
    @php
        $soalTerurut = $tugas->soalTugas->sortBy('urutan');
        $tipeLabels = ['pilihan_ganda' => 'Pilihan Ganda', 'esai' => 'Esai', 'upload_file' => 'Upload File'];
        $adding = old('_soal_form') === 'create';
        $addType = $adding ? old('tipe_soal', 'pilihan_ganda') : 'pilihan_ganda';
    @endphp

    <div class="ews-crud">
        <a href="{{ route('guru_mapel.tugas.index') }}" class="ews-back-link">← Kembali ke daftar tugas</a>

        <header class="mb-7 border-b border-gray-200 pb-6">
            <div class="mb-3 flex flex-wrap items-center gap-2">
                <span class="text-xs font-semibold uppercase tracking-wide text-gray-500">Detail tugas</span>
                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $tugas->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                    {{ $tugas->is_published ? 'Dipublikasi' : 'Draft' }}
                </span>
            </div>
            <div class="ews-crud-toolbar border-0 pb-0">
                <h1>{{ $tugas->judul }}</h1>
            </div>
            @if ($tugas->deskripsi)
                <p class="mb-5 whitespace-pre-line break-words text-sm leading-relaxed text-gray-600">{{ $tugas->deskripsi }}</p>
            @endif
            <dl class="mb-6 grid gap-4 rounded-lg bg-gray-50 p-4 text-sm sm:grid-cols-3">
                <div>
                    <dt class="mb-1 text-xs text-gray-500">Periode pengerjaan</dt>
                    <dd class="font-semibold text-gray-800">{{ $tugas->tanggal_mulai->translatedFormat('d M Y') }} – {{ $tugas->tanggal_selesai->translatedFormat('d M Y') }}</dd>
                </div>
                <div>
                    <dt class="mb-1 text-xs text-gray-500">Jumlah soal</dt>
                    <dd class="font-semibold text-gray-800">{{ $soalTerurut->count() }} soal</dd>
                </div>
                <div>
                    <dt class="mb-1 text-xs text-gray-500">Total poin</dt>
                    <dd class="font-semibold text-gray-800">{{ $soalTerurut->sum('poin') }} poin</dd>
                </div>
            </dl>
            <div class="flex flex-wrap items-center gap-2">
                <form action="{{ route('guru_mapel.tugas.publish', $tugas) }}" method="POST" class="max-sm:w-full">
                    @csrf
                    <button type="submit" class="ews-button ews-button-primary max-sm:w-full"
                        @disabled(! $tugas->is_published && $soalTerurut->isEmpty())>
                        {{ $tugas->is_published ? 'Tarik Publikasi' : 'Publikasikan' }}
                    </button>
                </form>
                @if ($tugas->is_published)
                    <a href="{{ route('guru_mapel.tugas.siswa', $tugas) }}" class="ews-button ews-button-secondary max-sm:w-full">Lihat Progress Siswa</a>
                @endif
                <a href="{{ route('guru_mapel.tugas.edit', $tugas) }}" class="ews-button ews-button-secondary max-sm:flex-1">Edit Tugas</a>
                <form action="{{ route('guru_mapel.tugas.destroy', $tugas) }}" method="POST" class="max-sm:flex-1"
                    onsubmit="return confirm('Hapus tugas ini beserta soal di dalamnya? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="ews-button ews-button-danger max-sm:w-full">Hapus Tugas</button>
                </form>
            </div>
            @if (! $tugas->is_published)
                <p class="mt-3 text-xs leading-relaxed text-gray-500">
                    {{ $soalTerurut->isEmpty() ? 'Tambahkan minimal satu soal sebelum mempublikasikan tugas.' : 'Periksa soal dan kunci jawaban sebelum mempublikasikan tugas.' }}
                    Progress siswa tersedia setelah tugas dipublikasikan.
                </p>
            @endif
        </header>

        <section aria-labelledby="daftar-soal-heading" class="space-y-5">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 id="daftar-soal-heading" class="text-lg font-semibold text-gray-900">Daftar soal</h2>
                    <p class="mt-1 text-sm text-gray-500">Susun pertanyaan, pilihan jawaban, dan poin untuk tugas ini.</p>
                </div>
                <a href="#tambah-soal" class="ews-button ews-button-secondary" data-open-add-soal>+ Tambah Soal</a>
            </div>

            @forelse ($soalTerurut as $soal)
                @php
                    $editMarker = 'edit-' . $soal->id;
                    $editing = old('_soal_form') === $editMarker;
                    $editValue = static fn (string $field) => $editing ? old($field, $soal->{$field}) : $soal->{$field};
                    $editType = $editValue('tipe_soal');
                    $isEditPg = $editType === 'pilihan_ganda';
                @endphp
                <article class="min-w-0 rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-5" aria-labelledby="soal-heading-{{ $soal->id }}">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 id="soal-heading-{{ $soal->id }}" class="font-semibold text-gray-900">Soal {{ $soal->urutan }}</h3>
                            <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">{{ $tipeLabels[$soal->tipe_soal] ?? $soal->tipe_soal }}</span>
                        </div>
                        <span class="text-sm font-semibold text-gray-600">{{ $soal->poin }} poin</span>
                    </div>
                    <p class="whitespace-pre-line break-words text-sm leading-relaxed text-gray-800">{{ $soal->pertanyaan }}</p>

                    @if ($soal->tipe_soal === 'pilihan_ganda')
                        <ul class="mt-4 grid gap-2 sm:grid-cols-2" aria-label="Pilihan jawaban soal {{ $soal->urutan }}">
                            @foreach (['a', 'b', 'c', 'd'] as $opsi)
                                @php($isKey = $soal->kunci_jawaban === $opsi)
                                <li class="flex min-w-0 items-start gap-3 rounded-lg border px-3 py-3 text-sm {{ $isKey ? 'border-green-200 bg-green-50 text-green-900' : 'border-gray-200 text-gray-600' }}">
                                    <span class="font-semibold">{{ strtoupper($opsi) }}.</span>
                                    <span class="min-w-0 flex-1 break-words">{{ $soal->{'opsi_' . $opsi} }}</span>
                                    @if ($isKey)
                                        <span class="shrink-0 text-xs font-semibold">Kunci</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <details class="mt-5 border-t border-gray-100 pt-4" id="edit-soal-{{ $soal->id }}" @if ($editing) open @endif>
                        <summary class="cursor-pointer text-sm font-semibold text-green-800">Edit soal {{ $soal->urutan }}</summary>
                        <form action="{{ route('guru_mapel.tugas.soal.update', [$tugas, $soal]) }}" method="POST" class="mt-5" data-soal-form>
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="_soal_form" value="{{ $editMarker }}">
                            @if ($editing && $errors->any())
                                <div class="ews-notice ews-notice-error" role="alert">
                                    <strong>Periksa kembali isian soal {{ $soal->urutan }}.</strong>
                                    <ul class="mt-2 list-disc pl-5">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="ews-fields">
                                <div>
                                    <label class="block" for="edit-{{ $soal->id }}-tipe_soal">Tipe soal <span class="text-red-700">*</span></label>
                                    <select id="edit-{{ $soal->id }}-tipe_soal" name="tipe_soal" required data-tipe-soal aria-controls="edit-{{ $soal->id }}-opsi" aria-invalid="{{ $editing && $errors->has('tipe_soal') ? 'true' : 'false' }}">
                                        @foreach ($tipeLabels as $value => $label)
                                            <option value="{{ $value }}" @selected($editType === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block" for="edit-{{ $soal->id }}-poin">Poin <span class="text-red-700">*</span></label>
                                    <input id="edit-{{ $soal->id }}-poin" type="number" name="poin" min="0" step="1" value="{{ $editValue('poin') }}" required aria-invalid="{{ $editing && $errors->has('poin') ? 'true' : 'false' }}">
                                </div>
                                <div>
                                    <label class="block" for="edit-{{ $soal->id }}-pertanyaan">Pertanyaan <span class="text-red-700">*</span></label>
                                    <textarea id="edit-{{ $soal->id }}-pertanyaan" name="pertanyaan" rows="4" required aria-invalid="{{ $editing && $errors->has('pertanyaan') ? 'true' : 'false' }}">{{ $editValue('pertanyaan') }}</textarea>
                                </div>
                            </div>
                            <fieldset id="edit-{{ $soal->id }}-opsi" class="mt-5 min-w-0 rounded-lg border border-gray-200 p-4" data-pg-fields @if (! $isEditPg) hidden disabled @endif>
                                <legend class="px-2 text-sm font-semibold text-gray-700">Pilihan jawaban</legend>
                                <div class="ews-fields">
                                    @foreach (['a', 'b', 'c', 'd'] as $opsi)
                                        @php($field = 'opsi_' . $opsi)
                                        <div>
                                            <label class="block" for="edit-{{ $soal->id }}-{{ $field }}">Opsi {{ strtoupper($opsi) }} <span class="text-red-700">*</span></label>
                                            <input id="edit-{{ $soal->id }}-{{ $field }}" type="text" name="{{ $field }}" value="{{ $editValue($field) }}" @required($isEditPg) aria-invalid="{{ $editing && $errors->has($field) ? 'true' : 'false' }}">
                                        </div>
                                    @endforeach
                                    <div>
                                        <label class="block" for="edit-{{ $soal->id }}-kunci_jawaban">Kunci jawaban <span class="text-red-700">*</span></label>
                                        <select id="edit-{{ $soal->id }}-kunci_jawaban" name="kunci_jawaban" @required($isEditPg) aria-invalid="{{ $editing && $errors->has('kunci_jawaban') ? 'true' : 'false' }}">
                                            <option value="">Pilih kunci jawaban</option>
                                            @foreach (['a', 'b', 'c', 'd'] as $opsi)
                                                <option value="{{ $opsi }}" @selected($editValue('kunci_jawaban') === $opsi)>{{ strtoupper($opsi) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </fieldset>
                            <div class="ews-form-actions">
                                <button type="submit" class="ews-button ews-button-primary">Simpan Perubahan Soal</button>
                                <button type="button" class="ews-button ews-button-secondary" data-close-soal>Batal</button>
                            </div>
                        </form>
                    </details>
                    <form action="{{ route('guru_mapel.tugas.soal.destroy', [$tugas, $soal]) }}" method="POST" class="mt-4"
                        onsubmit="return confirm('Hapus soal ini? Tindakan ini tidak dapat dibatalkan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="ews-button ews-button-danger" aria-label="Hapus soal {{ $soal->urutan }}">Hapus Soal</button>
                    </form>
                </article>
            @empty
                <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 px-5 py-9 text-center">
                    <h3 class="font-semibold text-gray-800">Belum ada soal</h3>
                    <p class="mt-2 text-sm text-gray-500">Mulai dengan menambahkan soal pilihan ganda, esai, atau upload file.</p>
                </div>
            @endforelse
        </section>

        <details id="tambah-soal" class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-4 sm:p-5" @if ($adding || $soalTerurut->isEmpty()) open @endif>
            <summary class="cursor-pointer text-base font-semibold text-green-800">Tambah soal baru</summary>
            <p class="mt-3 text-sm text-gray-500">Isian bertanda <span class="text-red-700">*</span> wajib diisi. Pilihan jawaban hanya diperlukan untuk soal pilihan ganda.</p>
            <form action="{{ route('guru_mapel.tugas.soal.store', $tugas) }}" method="POST" class="mt-5" data-soal-form>
                @csrf
                <input type="hidden" name="_soal_form" value="create">
                @if ($adding && $errors->any())
                    <div class="ews-notice ews-notice-error" role="alert">
                        <strong>Soal belum ditambahkan. Periksa kembali isian berikut.</strong>
                        <ul class="mt-2 list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="ews-fields">
                    <div>
                        <label class="block" for="create-tipe_soal">Tipe soal <span class="text-red-700">*</span></label>
                        <select id="create-tipe_soal" name="tipe_soal" required data-tipe-soal aria-controls="create-opsi" aria-invalid="{{ $adding && $errors->has('tipe_soal') ? 'true' : 'false' }}">
                            @foreach ($tipeLabels as $value => $label)
                                <option value="{{ $value }}" @selected($addType === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block" for="create-poin">Poin <span class="text-red-700">*</span></label>
                        <input id="create-poin" type="number" name="poin" min="0" step="1" value="{{ $adding ? old('poin', 10) : 10 }}" required aria-invalid="{{ $adding && $errors->has('poin') ? 'true' : 'false' }}">
                    </div>
                    <div>
                        <label class="block" for="create-pertanyaan">Pertanyaan <span class="text-red-700">*</span></label>
                        <textarea id="create-pertanyaan" name="pertanyaan" rows="4" required placeholder="Tuliskan pertanyaan atau instruksi pengerjaan yang jelas." aria-invalid="{{ $adding && $errors->has('pertanyaan') ? 'true' : 'false' }}">{{ $adding ? old('pertanyaan') : '' }}</textarea>
                    </div>
                </div>
                <fieldset id="create-opsi" class="mt-5 min-w-0 rounded-lg border border-gray-200 p-4" data-pg-fields @if ($addType !== 'pilihan_ganda') hidden disabled @endif>
                    <legend class="px-2 text-sm font-semibold text-gray-700">Pilihan jawaban</legend>
                    <div class="ews-fields">
                        @foreach (['a', 'b', 'c', 'd'] as $opsi)
                            @php($field = 'opsi_' . $opsi)
                            <div>
                                <label class="block" for="create-{{ $field }}">Opsi {{ strtoupper($opsi) }} <span class="text-red-700">*</span></label>
                                <input id="create-{{ $field }}" type="text" name="{{ $field }}" value="{{ $adding ? old($field) : '' }}" @required($addType === 'pilihan_ganda') aria-invalid="{{ $adding && $errors->has($field) ? 'true' : 'false' }}">
                            </div>
                        @endforeach
                        <div>
                            <label class="block" for="create-kunci_jawaban">Kunci jawaban <span class="text-red-700">*</span></label>
                            <select id="create-kunci_jawaban" name="kunci_jawaban" @required($addType === 'pilihan_ganda') aria-invalid="{{ $adding && $errors->has('kunci_jawaban') ? 'true' : 'false' }}">
                                <option value="">Pilih kunci jawaban</option>
                                @foreach (['a', 'b', 'c', 'd'] as $opsi)
                                    <option value="{{ $opsi }}" @selected($adding && old('kunci_jawaban') === $opsi)>{{ strtoupper($opsi) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </fieldset>
                <div class="ews-form-actions">
                    <button type="submit" class="ews-button ews-button-primary">Tambah Soal</button>
                    <button type="button" class="ews-button ews-button-secondary" data-close-soal>Tutup</button>
                </div>
            </form>
        </details>
    </div>

    <script>
        document.querySelectorAll('[data-soal-form]').forEach(function (form) {
            var typeSelect = form.querySelector('[data-tipe-soal]');
            var choiceFields = form.querySelector('[data-pg-fields]');

            function updateChoiceFields() {
                var isMultipleChoice = typeSelect.value === 'pilihan_ganda';
                choiceFields.hidden = !isMultipleChoice;
                choiceFields.disabled = !isMultipleChoice;
                choiceFields.querySelectorAll('input, select').forEach(function (field) {
                    field.required = isMultipleChoice;
                });
            }

            typeSelect.addEventListener('change', updateChoiceFields);
            updateChoiceFields();
        });

        document.querySelectorAll('[data-close-soal]').forEach(function (button) {
            button.addEventListener('click', function () {
                var details = button.closest('details');
                details.open = false;
                details.querySelector('summary').focus();
            });
        });

        document.querySelectorAll('[data-open-add-soal]').forEach(function (link) {
            link.addEventListener('click', function () {
                var details = document.getElementById('tambah-soal');
                details.open = true;
                details.querySelector('summary').focus();
            });
        });
    </script>
@endsection
