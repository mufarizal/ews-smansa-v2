<?php

use App\Models\GuruMapelKelas;
use App\Models\JawabanTugas;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\NilaiTugas;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\SoalTugas;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\ViewErrorBag;

beforeEach(function () {
    $this->withoutVite();
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

function tugasViewData(string $status = 'sedang_dikerjakan'): array
{
    $mapel = (new Mapel)->forceFill(['id' => 1, 'nama' => 'Matematika']);
    $kelas = (new Kelas)->forceFill(['id' => 1, 'nama' => 'X-A']);
    $tugas = (new Tugas)->forceFill([
        'id' => 10, 'judul' => 'Latihan pecahan', 'deskripsi' => 'Petunjuk <script>alert(1)</script>',
        'tanggal_mulai' => now()->subDay(), 'tanggal_selesai' => now()->addDays(3), 'is_published' => true,
    ]);
    $tugas->setRelation('mapel', $mapel)->setRelation('kelas', $kelas);
    $soalList = collect(['pilihan_ganda', 'esai', 'upload_file'])->map(fn ($tipe, $i) => (new SoalTugas)->forceFill([
        'id' => $i + 1, 'tugas_id' => 10, 'urutan' => $i + 1, 'tipe_soal' => $tipe,
        'pertanyaan' => 'Pertanyaan '.($i + 1), 'poin' => 10,
        'opsi_a' => 'Pilihan satu', 'opsi_b' => 'Pilihan dua', 'opsi_c' => 'Pilihan tiga',
        'opsi_d' => 'Pilihan empat', 'kunci_jawaban' => 'b',
    ])
    );
    $tugas->setRelation('soalTugas', $soalList);
    $jawabanList = collect([
        (new JawabanTugas)->forceFill(['soal_tugas_id' => 1, 'jawaban_teks' => 'a', 'poin_diperoleh' => 0]),
        (new JawabanTugas)->forceFill(['soal_tugas_id' => 2, 'jawaban_teks' => 'Jawaban esai <b>uji</b>', 'poin_diperoleh' => null]),
        (new JawabanTugas)->forceFill(['soal_tugas_id' => 3, 'file_path' => 'tugas-jawaban/contoh.pdf', 'poin_diperoleh' => 5]),
    ])->keyBy('soal_tugas_id');
    $siswas = collect(range(1, 4))->map(fn ($id) => (new Siswa)->forceFill(['id' => $id, 'nis' => '100'.$id, 'nama' => 'Siswa '.$id]));
    $siswa = $siswas->first();
    $nilaiList = collect(['sedang_dikerjakan', 'menunggu_penilaian', 'selesai'])->mapWithKeys(fn ($s, $i) => [$i + 2 => (new NilaiTugas)->forceFill(['status' => $s, 'nilai' => $s === 'selesai' ? 85 : null])]);
    $nilaiTugas = (new NilaiTugas)->forceFill(['status' => $status, 'nilai' => $status === 'selesai' ? 85 : null]);
    $tugas->setAttribute('nilai_tugas', $nilaiTugas);
    $penugasan = (new GuruMapelKelas)->forceFill(['id' => 1]);
    $penugasan->setRelation('mapel', $mapel)->setRelation('kelas', $kelas);
    $penugasans = collect([$penugasan]);
    $tugasList = new LengthAwarePaginator([$tugas], 1, 10);

    return compact('tugas', 'tugasList', 'penugasans', 'siswas', 'siswa', 'nilaiList', 'nilaiTugas', 'soalList', 'jawabanList');
}

function tugasViewDocument(string $html): DOMXPath
{
    $document = new DOMDocument;
    @$document->loadHTML('<?xml encoding="UTF-8">'.$html);

    return new DOMXPath($document);
}

test('all requested task views render with supplied data', function (string $view) {
    $data = tugasViewData();
    if ($view === 'siswa.tugas.index') {
        $data['tugasList'] = collect([$data['tugas']]);
    }
    $html = view($view, $data)->render();
    expect($html)->toContain('ews-crud');
    expect($html)->not->toContain('<script>alert(1)</script>');
})->with([
    'guru_mapel.tugas.index', 'guru_mapel.tugas.create', 'guru_mapel.tugas.show',
    'guru_mapel.tugas.edit', 'guru_mapel.tugas.siswa', 'guru_mapel.tugas.nilai',
    'siswa.tugas.index', 'siswa.tugas.show',
]);

test('submitted student task has no answer or submission forms', function (string $status) {
    $html = view('siswa.tugas.show', tugasViewData($status))->render();
    $xpath = tugasViewDocument($html);
    expect($xpath->query('//main//form')->length)->toBe(0);
    expect($xpath->query('//main//input | //main//textarea | //main//select')->length)->toBe(0);
    expect($html)->not->toContain('Kumpulkan Tugas');
    expect($html)->toContain('Jawaban esai &lt;b&gt;uji&lt;/b&gt;');
})->with(['menunggu_penilaian', 'selesai']);

test('student saves each answer separately before final submission', function () {
    $data = tugasViewData();
    $html = view('siswa.tugas.show', $data)->render();
    $xpath = tugasViewDocument($html);
    expect($xpath->query('//main//form')->length)->toBe(4);
    foreach ($data['soalList'] as $soal) {
        $url = route('siswa.tugas.jawab.store', [$data['tugas'], $soal]);
        expect($xpath->query('//main//form[@action="'.$url.'"]')->length)->toBe(1);
    }
    expect($xpath->query('//input[@type="radio" and @checked and @value="a"]')->length)->toBe(1);
    expect($xpath->query('//form[@enctype="multipart/form-data"]//input[@name="file"]')->length)->toBe(1);
    expect($html)->toContain('contoh.pdf')->not->toContain('kunci_jawaban');
});

test('only manual questions expose grading inputs with score limits', function () {
    $html = view('guru_mapel.tugas.nilai', tugasViewData())->render();
    $xpath = tugasViewDocument($html);
    expect($xpath->query('//input[@name="poin[1]"]')->length)->toBe(0);
    foreach ([2, 3] as $id) {
        $input = $xpath->query('//input[@name="poin['.$id.']"]')->item(0);
        expect($input)->not->toBeNull();
        expect($input->getAttribute('max'))->toBe('10');
        expect($input->hasAttribute('required'))->toBeFalse();
    }
    expect($html)->toContain('contoh.pdf');
});

test('progress enables grading only for waiting or finished students', function () {
    $data = tugasViewData();
    $html = view('guru_mapel.tugas.siswa', $data)->render();
    foreach ($data['siswas'] as $siswa) {
        $url = route('guru_mapel.tugas.nilai.form', [$data['tugas'], $siswa]);
        if ($siswa->id >= 3) {
            expect($html)->toContain($url);
        } else {
            expect($html)->not->toContain($url);
        }
    }
});

test('editing one question restores old values only in its own form', function () {
    session()->flashInput(['_soal_form' => 'edit-2', 'pertanyaan' => 'Revisi esai', 'tipe_soal' => 'esai', 'poin' => 8]);
    $html = view('guru_mapel.tugas.show', tugasViewData())->render();
    $xpath = tugasViewDocument($html);
    expect($xpath->query('//textarea[@id="edit-2-pertanyaan"]')->item(0)->textContent)->toBe('Revisi esai');
    expect($xpath->query('//textarea[@id="edit-1-pertanyaan"]')->item(0)->textContent)->toBe('Pertanyaan 1');
    expect($xpath->query('//details[@id="edit-soal-2" and @open]')->length)->toBe(1);
    expect($xpath->query('//fieldset[@id="edit-2-opsi" and @hidden and @disabled]')->length)->toBe(1);
    expect($xpath->query('//fieldset[@id="edit-1-opsi" and not(@hidden)]//input[@required]')->length)->toBe(4);
});

test('choice-only grading shows automatic scores without a manual submit', function () {
    $data = tugasViewData();
    $data['soalList'] = $data['soalList']->take(1);
    $html = view('guru_mapel.tugas.nilai', $data)->render();
    expect(tugasViewDocument($html)->query('//main//form')->length)->toBe(0);
});

test('empty assignment lists and unassigned teacher create render useful empty states', function () {
    $data = tugasViewData();
    $data['tugasList'] = new LengthAwarePaginator([], 0, 10);
    $data['penugasans'] = collect();
    expect(view('guru_mapel.tugas.index', $data)->render())->toContain('Belum ada tugas');
    $create = tugasViewDocument(view('guru_mapel.tugas.create', $data)->render());
    expect($create->query('//main//button[@type="submit" and @disabled]')->length)->toBe(1);
});
