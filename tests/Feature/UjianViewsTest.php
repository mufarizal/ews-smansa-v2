<?php

use App\Models\GuruMapelKelas;
use App\Models\HasilUjian;
use App\Models\JawabanUjian;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\SoalUjian;
use App\Models\UjianHarian;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\ViewErrorBag;

beforeEach(function () {
    $this->withoutVite();
    $this->travelTo(Carbon::parse('2026-09-15 09:00:00'));
    $user = (new User)->forceFill(['id' => 1, 'name' => 'Pengguna Uji']);
    $user->setRelation('roles', collect([
        (new Role)->forceFill(['id' => 1, 'name' => 'guru_mapel', 'label' => 'Guru Mapel']),
        (new Role)->forceFill(['id' => 2, 'name' => 'siswa', 'label' => 'Siswa']),
    ]));
    $this->actingAs($user);
    session()->start();
    request()->setLaravelSession(session()->driver());
    view()->share('errors', new ViewErrorBag);
});

function ujianViewData(string $status = 'sedang_mengerjakan'): array
{
    $mapel = (new Mapel)->forceFill(['id' => 1, 'nama' => 'Matematika']);
    $kelas = (new Kelas)->forceFill(['id' => 1, 'nama' => 'X-A']);
    $ujian = (new UjianHarian)->forceFill([
        'id' => 10, 'judul' => 'Ujian pecahan', 'deskripsi' => 'Petunjuk <script>alert(1)</script>',
        'tanggal' => now(), 'durasi_menit' => 60, 'is_published' => true,
    ]);
    $ujian->setRelation('mapel', $mapel)->setRelation('kelas', $kelas);
    $soalList = collect(['pilihan_ganda', 'esai'])->map(fn ($tipe, $i) => (new SoalUjian)->forceFill([
        'id' => $i + 1, 'ujian_harian_id' => 10, 'urutan' => $i + 1, 'tipe_soal' => $tipe,
        'pertanyaan' => 'Pertanyaan '.($i + 1), 'poin' => 10,
        'opsi_a' => 'Pilihan satu', 'opsi_b' => 'Pilihan dua', 'opsi_c' => 'Pilihan tiga',
        'opsi_d' => 'Pilihan empat', 'kunci_jawaban' => $tipe === 'pilihan_ganda' ? 'B' : null,
    ]));
    $ujian->setRelation('soalUjians', $soalList);
    $jawabanList = collect([
        (new JawabanUjian)->forceFill(['soal_ujian_id' => 1, 'jawaban_teks' => 'A', 'poin_diperoleh' => 0]),
        (new JawabanUjian)->forceFill(['soal_ujian_id' => 2, 'jawaban_teks' => 'Jawaban esai <b>uji</b>', 'poin_diperoleh' => null]),
    ])->keyBy('soal_ujian_id');
    $siswas = collect(range(1, 4))->map(fn ($id) => (new Siswa)->forceFill(['id' => $id, 'nis' => '100'.$id, 'nama' => 'Siswa '.$id]));
    $siswa = $siswas->first();
    $hasilList = collect(['sedang_mengerjakan', 'menunggu_penilaian', 'selesai'])->mapWithKeys(fn ($s, $i) => [
        $i + 2 => (new HasilUjian)->forceFill([
            'siswa_id' => $i + 2, 'status' => $s, 'nilai' => 85, 'waktu_mulai' => now()->subMinutes(20),
        ]),
    ]);
    $hasil = (new HasilUjian)->forceFill([
        'status' => $status, 'nilai' => $status === 'selesai' ? 85 : null, 'waktu_mulai' => now()->subMinutes(20),
    ]);
    $hasil->setRelation('ujianHarian', $ujian);
    $ujian->setAttribute('hasil', $hasil);
    $penugasan = (new GuruMapelKelas)->forceFill(['id' => 1]);
    $penugasan->setRelation('mapel', $mapel)->setRelation('kelas', $kelas);
    $penugasans = collect([$penugasan]);
    $ujianList = new LengthAwarePaginator([$ujian], 1, 10);
    $jumlahSoal = $soalList->count();

    return compact('ujian', 'ujianList', 'penugasans', 'siswas', 'siswa', 'hasilList', 'hasil', 'soalList', 'jawabanList', 'jumlahSoal');
}

function ujianViewDocument(string $html): DOMXPath
{
    $document = new DOMDocument;
    @$document->loadHTML('<?xml encoding="UTF-8">'.$html);

    return new DOMXPath($document);
}

test('all requested exam views render supplied models without unescaped descriptions', function (string $view) {
    $data = ujianViewData();
    if ($view === 'siswa.ujian.index') {
        $data['ujianList'] = collect([$data['ujian']]);
    }
    $html = view($view, $data)->render();
    expect($html)->toContain('ews-crud')->not->toContain('<script>alert(1)</script>');
})->with([
    'guru_mapel.ujian.index', 'guru_mapel.ujian.create', 'guru_mapel.ujian.show',
    'guru_mapel.ujian.edit', 'guru_mapel.ujian.hasil', 'guru_mapel.ujian.nilai',
    'siswa.ujian.index', 'siswa.ujian.mulai', 'siswa.ujian.kerjakan', 'siswa.ujian.selesai',
]);

test('student saves exam answers separately with uppercase choices and saved values', function () {
    $data = ujianViewData();
    $html = view('siswa.ujian.kerjakan', $data)->render();
    $xpath = ujianViewDocument($html);
    expect($xpath->query('//main//form')->length)->toBe(4);
    foreach ($data['soalList'] as $soal) {
        $url = route('siswa.ujian.jawab.store', [$data['ujian'], $soal]);
        $forms = $xpath->query('//main//form[@action="'.$url.'" and @method="POST"]');
        expect($forms->length)->toBe(1);
        expect($xpath->query('.//input[@name="_token"]', $forms->item(0))->length)->toBe(1);
    }
    expect($xpath->query('//input[@type="radio"]')->length)->toBe(4);
    foreach (['A', 'B', 'C', 'D'] as $choice) {
        expect($xpath->query('//input[@type="radio" and @name="jawaban_teks" and @value="'.$choice.'"]')->length)->toBe(1);
    }
    expect($xpath->query('//input[@type="radio" and @checked and @value="A"]')->length)->toBe(1);
    expect($xpath->query('//textarea[@name="jawaban_teks"]')->item(0)->textContent)->toBe('Jawaban esai <b>uji</b>');
    expect($html)->toContain('Jawaban esai &lt;b&gt;uji&lt;/b&gt;')->not->toContain('kunci_jawaban', 'Kunci jawaban', 'Poin otomatis');
});

test('active exam has a server-derived countdown and separate manual and automatic POST forms', function () {
    $data = ujianViewData();
    $xpath = ujianViewDocument(view('siswa.ujian.kerjakan', $data)->render());
    expect($xpath->query('//*[@id="exam-work"]')->item(0)->getAttribute('data-exam-seconds'))->toBe('2400');
    expect($xpath->query('//*[@id="exam-countdown"]')->item(0)->textContent)->toBe('40:00');
    $url = route('siswa.ujian.submit', $data['ujian']);
    expect($xpath->query('//main//form[@action="'.$url.'" and @method="POST"]')->length)->toBe(2);
    $automatic = $xpath->query('//form[@id="exam-auto-submit-form" and @hidden]')->item(0);
    expect($automatic)->not->toBeNull();
    expect($automatic->hasAttribute('onsubmit'))->toBeFalse();
    expect($xpath->query('//form[@id="exam-submit-form"]')->item(0)->getAttribute('onsubmit'))->toContain('confirm(');
});

test('urgent and expired exam timers render exact remaining time', function (int $remaining, string $formatted) {
    $data = ujianViewData();
    $data['hasil']->waktu_mulai = now()->subSeconds(3600 - $remaining);
    $xpath = ujianViewDocument(view('siswa.ujian.kerjakan', $data)->render());
    expect($xpath->query('//*[@id="exam-work"]')->item(0)->getAttribute('data-exam-seconds'))->toBe((string) $remaining);
    expect($xpath->query('//*[@id="exam-countdown"]')->item(0)->textContent)->toBe($formatted);
    expect($xpath->query('//*[@id="exam-timer"]')->item(0)->getAttribute('class'))->toContain('bg-red-50');
})->with([[299, '04:59'], [0, '00:00']]);

test('finished exam states expose no active answer or submit controls', function (string $status) {
    $data = ujianViewData($status);
    $html = view('siswa.ujian.kerjakan', $data)->render();
    $xpath = ujianViewDocument($html);
    expect($xpath->query('//main//form | //main//input | //main//textarea | //main//select | //main//script')->length)->toBe(0);
    expect($html)->not->toContain('exam-work', 'exam-countdown');
    expect($html)->toContain(route('siswa.ujian.selesai', $data['ujian']));
})->with(['menunggu_penilaian', 'selesai']);

test('starting an exam uses a confirmed POST with the irreversible timer warning', function () {
    $data = ujianViewData();
    $html = view('siswa.ujian.mulai', $data)->render();
    $xpath = ujianViewDocument($html);
    $url = route('siswa.ujian.mulai', $data['ujian']);
    $form = $xpath->query('//main//form[@action="'.$url.'" and @method="POST"]')->item(0);
    expect($form)->not->toBeNull();
    expect($form->getAttribute('onsubmit'))->toContain('confirm(');
    expect($xpath->query('.//input[@name="_token"]', $form)->length)->toBe(1);
    expect($html)->toContain('TIDAK bisa dijeda atau diulang dari awal.', 'Matematika', '2 soal', '60 menit');
});

test('grading inputs only target essays and preserve blank scores with point limits', function () {
    $data = ujianViewData();
    $html = view('guru_mapel.ujian.nilai', $data)->render();
    $xpath = ujianViewDocument($html);
    expect($xpath->query('//input[@name="poin[1]"]')->length)->toBe(0);
    $input = $xpath->query('//input[@name="poin[2]"]')->item(0);
    expect($input)->not->toBeNull();
    expect($input->getAttribute('min'))->toBe('0');
    expect($input->getAttribute('max'))->toBe('10');
    expect($input->getAttribute('value'))->toBe('');
    expect($input->hasAttribute('required'))->toBeFalse();
    expect($html)->toContain('Poin otomatis:', 'Kunci jawaban', 'Jawaban esai &lt;b&gt;uji&lt;/b&gt;');
    $url = route('guru_mapel.ujian.nilai.store', [$data['ujian'], $data['siswa']]);
    expect($xpath->query('//main//form[@action="'.$url.'" and @method="POST"]')->length)->toBe(1);
});

test('choice-only exams show automatic scores without a manual grading form', function () {
    $data = ujianViewData();
    $data['soalList'] = $data['soalList']->take(1);
    $html = view('guru_mapel.ujian.nilai', $data)->render();
    expect(ujianViewDocument($html)->query('//main//form | //main//input')->length)->toBe(0);
    expect($html)->toContain('Semua soal pilihan ganda dinilai otomatis.');
});

test('teacher results show statuses start times and grading links only after submission', function () {
    $data = ujianViewData();
    $html = view('guru_mapel.ujian.hasil', $data)->render();
    $xpath = ujianViewDocument($html);
    $rows = $xpath->query('//main//tbody/tr');
    expect($rows->length)->toBe(4);
    foreach ($data['siswas'] as $index => $siswa) {
        $url = route('guru_mapel.ujian.nilai.form', [$data['ujian'], $siswa]);
        expect($xpath->query('.//a[@href="'.$url.'"]', $rows->item($index))->length)->toBe($siswa->id >= 3 ? 1 : 0);
        $cells = $xpath->query('./td', $rows->item($index));
        expect(trim($cells->item(3)->textContent))->toContain($siswa->id === 1 ? '-' : '08:40');
        expect(trim($cells->item(4)->textContent))->toBe($siswa->id === 4 ? '85.00' : '-');
    }
    expect($html)->toContain('Belum Mulai', 'Sedang Dikerjakan', 'Menunggu penilaian', 'Selesai');
});

test('student exam list always links to show with the correct status action', function (?string $status, string $action, string $badge) {
    $data = ujianViewData($status ?? 'sedang_mengerjakan');
    if ($status === null) {
        $data['ujian']->setAttribute('hasil', null);
    }
    $data['ujianList'] = collect([$data['ujian']]);
    $html = view('siswa.ujian.index', $data)->render();
    $xpath = ujianViewDocument($html);
    $url = route('siswa.ujian.show', $data['ujian']);
    expect(trim($xpath->query('//main//article//a[@href="'.$url.'"]')->item(0)->textContent))->toBe($action);
    expect($html)->toContain($badge);
    $score = trim($xpath->query('//main//article//strong')->item(0)->textContent);
    expect($score)->toBe($status === 'selesai' ? '85.00' : '-');
})->with([
    [null, 'Mulai', 'Belum Dimulai'],
    ['sedang_mengerjakan', 'Lanjutkan', 'Sedang Dikerjakan'],
    ['menunggu_penilaian', 'Lihat Hasil', 'Menunggu Penilaian'],
    ['selesai', 'Lihat Hasil', 'Selesai'],
]);

test('completed exam page distinguishes final score from waiting for essay grading', function (string $status, string $message) {
    $data = ujianViewData($status);
    $html = view('siswa.ujian.selesai', $data)->render();
    expect($html)->toContain($message, route('siswa.ujian.index'));
    expect(ujianViewDocument($html)->query('//main//form')->length)->toBe(0);
    if ($status === 'selesai') {
        expect($html)->toContain('85.00', 'Nilai akhir');
    } else {
        expect($html)->not->toContain('Nilai akhir');
    }
})->with([
    ['selesai', 'Ujian selesai dinilai'],
    ['menunggu_penilaian', 'Ujian sudah dikumpulkan, menunggu penilaian guru untuk soal esai.'],
]);

test('exam metadata forms follow backend field limits and preserve input', function () {
    session()->flashInput(['judul' => 'Judul setelah validasi', 'durasi_menit' => 90]);
    foreach (['create', 'edit'] as $page) {
        $xpath = ujianViewDocument(view('guru_mapel.ujian.'.$page, ujianViewData())->render());
        $title = $xpath->query('//main//input[@name="judul"]')->item(0);
        expect($title->getAttribute('maxlength'))->toBe('150');
        expect($title->getAttribute('value'))->toBe('Judul setelah validasi');
        $duration = $xpath->query('//main//input[@name="durasi_menit"]')->item(0);
        expect($duration->getAttribute('min'))->toBe('5');
        expect($duration->getAttribute('max'))->toBe('300');
        expect($duration->getAttribute('value'))->toBe('90');
        expect($xpath->query('//input[@name="tanggal" and @type="date"]')->length)->toBe(1);
    }
});

test('question editor isolates validation input and uses uppercase answer keys', function () {
    session()->flashInput(['_soal_form' => 'edit-2', 'pertanyaan' => 'Revisi esai', 'tipe_soal' => 'esai', 'poin' => 8]);
    $html = view('guru_mapel.ujian.show', ujianViewData())->render();
    $xpath = ujianViewDocument($html);
    expect($xpath->query('//textarea[@id="edit-2-pertanyaan"]')->item(0)->textContent)->toBe('Revisi esai');
    expect($xpath->query('//textarea[@id="edit-1-pertanyaan"]')->item(0)->textContent)->toBe('Pertanyaan 1');
    expect($xpath->query('//details[@id="edit-soal-2" and @open]')->length)->toBe(1);
    expect($xpath->query('//fieldset[@id="edit-2-opsi" and @hidden and @disabled]')->length)->toBe(1);
    expect($xpath->query('//fieldset[@id="edit-1-opsi" and not(@hidden)]//input[@required]')->length)->toBe(4);
    expect($xpath->query('//select[@id="edit-1-kunci_jawaban"]/option[@selected and @value="B"]')->length)->toBe(1);
    foreach (['A', 'B', 'C', 'D'] as $key) {
        expect($xpath->query('//select[@id="create-kunci_jawaban"]/option[@value="'.$key.'"]')->length)->toBe(1);
    }
    expect($xpath->query('//input[@name="poin" and @min="1"]')->length)->toBe(3);
    expect($html)->not->toContain('upload_file');
});

test('empty exam lists and unassigned teacher forms provide empty states', function () {
    $data = ujianViewData();
    $data['ujianList'] = new LengthAwarePaginator([], 0, 10);
    $data['penugasans'] = collect();
    expect(view('guru_mapel.ujian.index', $data)->render())->toContain('Belum ada ujian');
    expect(view('siswa.ujian.index', $data)->render())->toContain('Belum ada ujian');
    $create = ujianViewDocument(view('guru_mapel.ujian.create', $data)->render());
    expect($create->query('//main//button[@type="submit" and @disabled]')->length)->toBe(1);
    $data['siswas'] = collect();
    expect(view('guru_mapel.ujian.hasil', $data)->render())->toContain('Belum ada siswa di kelas ini');
});
