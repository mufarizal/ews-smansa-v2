<?php

use App\Models\Kelas;
use App\Models\Perilaku;
use App\Models\PerilakuSiswa;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Route;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

beforeEach(function () {
    $this->withoutVite();
    $this->travelTo(now()->setDate(2026, 9, 23)->startOfDay());
    $user = (new User)->forceFill(['id' => 1, 'name' => 'Guru Pengujian']);
    $user->setRelation('roles', collect(['guru_bk', 'guru_mapel', 'wali_kelas'])->map(fn ($name) => (new Role)->forceFill(['name' => $name, 'label' => $name])));
    $this->actingAs($user);
    session()->start();
    request()->setLaravelSession(session()->driver());
    view()->share('errors', new ViewErrorBag);
});

function perilakuViewData(): array
{
    $perilakus = collect([
        (new Perilaku)->forceFill(['id' => 1, 'nama' => 'Tertib', 'jenis' => 'positif', 'poin' => 5, 'is_default_aman' => true]),
        (new Perilaku)->forceFill(['id' => 2, 'nama' => 'Membantu teman', 'jenis' => 'positif', 'poin' => 10, 'is_default_aman' => false]),
        (new Perilaku)->forceFill(['id' => 3, 'nama' => 'Terlambat', 'jenis' => 'negatif', 'poin' => 2, 'is_default_aman' => false]),
    ]);
    $siswas = collect([(new Siswa)->forceFill(['id' => 1, 'nama' => 'Siswa A', 'nis' => '1001'])]);
    $kelasTerpilih = (new Kelas)->forceFill(['id' => 10, 'nama' => 'X-A']);
    $kelasTerpilih->setRelation('siswas', $siswas);
    $kelasList = collect([$kelasTerpilih]);
    $perilakuSiswa = (new PerilakuSiswa)->forceFill(['id' => 20, 'siswa_id' => 1, 'perilaku_id' => 3, 'tanggal' => '2026-09-22', 'catatan' => '<script>tidak aman</script>']);
    $perilakuSiswa->setRelation('siswa', $siswas->first())->setRelation('perilaku', $perilakus->last());
    $riwayat = new LengthAwarePaginator([$perilakuSiswa], 1, 15);
    $perilaku = $perilakus->last();
    $perilakuNegatif = $perilakus->where('jenis', 'negatif')->values();

    return compact('perilakus', 'siswas', 'kelasTerpilih', 'kelasList', 'perilakuSiswa', 'riwayat', 'perilaku', 'perilakuNegatif');
}

function perilakuDom(string $html): DOMXPath
{
    $document = new DOMDocument;
    @$document->loadHTML('<?xml encoding="UTF-8">'.$html);

    return new DOMXPath($document);
}

test('perilaku pages render with the required layout sections', function (string $view) {
    $data = perilakuViewData();
    if ($view === 'guru_bk.perilaku.index') {
        $data['perilakus'] = new LengthAwarePaginator($data['perilakus'], 3, 15);
    }
    $html = view($view, $data)->render();
    expect($html)->toContain('ews-crud')->not->toContain('<script>tidak aman</script>');
})->with([
    'guru_bk.perilaku.index', 'guru_bk.perilaku.create', 'guru_bk.perilaku.edit',
    'guru_mapel.perilaku.index', 'guru_mapel.perilaku.create', 'guru_mapel.perilaku.edit', 'guru_mapel.perilaku.bulk-aman', 'guru_mapel.perilaku.bulk-bermasalah',
    'wali_kelas.perilaku.index', 'wali_kelas.perilaku.create', 'wali_kelas.perilaku.edit', 'wali_kelas.perilaku.bulk-aman', 'wali_kelas.perilaku.bulk-bermasalah',
]);

test('only non-default positive behavior offers the default action', function () {
    $data = perilakuViewData();
    $data['perilakus'] = new LengthAwarePaginator($data['perilakus'], 3, 15);
    $html = view('guru_bk.perilaku.index', $data)->render();
    expect($html)->toContain('★ Default', route('guru_bk.perilaku.jadikan-default-aman', 2));
    expect($html)->not->toContain(route('guru_bk.perilaku.jadikan-default-aman', 1), route('guru_bk.perilaku.jadikan-default-aman', 3));
});

test('individual create shows student and class while edit keeps student immutable', function (string $role) {
    $data = perilakuViewData();
    $create = view($role.'.perilaku.create', $data)->render();
    expect($create)->toContain('Siswa A (X-A)', 'Terlambat (negatif, 2 poin)');
    $xpath = perilakuDom($create);
    expect($xpath->query('//select[@name="siswa_id"]/option[@value="1"]')->length)->toBe(1);
    expect($xpath->query('//input[@name="tanggal"]')->item(0)->getAttribute('max'))->toBe('2026-09-23');
    $edit = perilakuDom(view($role.'.perilaku.edit', $data)->render());
    expect($edit->query('//*[@name="siswa_id"]')->length)->toBe(0);
    expect($edit->query('//input[@name="_method" and @value="PUT"]')->length)->toBe(1);
})->with(['guru_mapel', 'wali_kelas']);

test('bulk issue form selects a class before showing a scoped POST checklist', function (string $role) {
    $data = perilakuViewData();
    $xpath = perilakuDom(view($role.'.perilaku.bulk-bermasalah', $data)->render());
    expect($xpath->query('//main//form[@method="GET"]//select[@name="kelas_id"]')->item(0)->getAttribute('onchange'))->toContain('submit');
    $post = $xpath->query('//main//form[@method="POST"]')->item(0);
    expect($post->getAttribute('action'))->toBe(route($role.'.perilaku.bulk-bermasalah.store'));
    expect($xpath->query('.//input[@name="kelas_id" and @value="10"]', $post)->length)->toBe(1);
    expect($xpath->query('.//input[@name="siswa_ids[]" and @value="1"]', $post)->length)->toBe(1);
    expect($xpath->query('.//select[@name="perilaku_id"]/option[@value="1"]', $post)->length)->toBe(0);
    $data['kelasTerpilih'] = null;
    $data['siswas'] = collect();
    expect(perilakuDom(view($role.'.perilaku.bulk-bermasalah', $data)->render())->query('//main//form[@method="POST"]')->length)->toBe(0);
})->with(['guru_mapel', 'wali_kelas']);

test('bulk safe requires confirmation and preserves validation input', function (string $role) {
    session()->flashInput(['kelas_id' => 10, 'tanggal' => '2026-09-21']);
    $xpath = perilakuDom(view($role.'.perilaku.bulk-aman', perilakuViewData())->render());
    $form = $xpath->query('//main//form[@method="POST"]')->item(0);
    expect($form->getAttribute('onsubmit'))->toContain('confirm(');
    expect($xpath->query('//input[@name="tanggal"]')->item(0)->getAttribute('value'))->toBe('2026-09-21');
    expect($xpath->query('//select[@name="kelas_id"]/option[@value="10" and @selected]')->length)->toBe(1);
})->with(['guru_mapel', 'wali_kelas']);

test('updated role dashboards retain attendance and show each flash once', function (string $role) {
    session()->flash('success', 'Pesan berhasil unik');
    $requestRoute = new Route('GET', '/'.$role.'/dashboard', []);
    $requestRoute->name($role.'.dashboard');
    request()->setRouteResolver(fn () => $requestRoute);
    $html = view($role.'.dashboard', ['kehadiran' => ['pagi' => null, 'sore' => null]])->render();
    expect($html)->toContain('Hadir Pagi', 'Hadir Sore');
    expect(substr_count($html, 'Pesan berhasil unik'))->toBe(1);
})->with(['guru_bk', 'wali_kelas']);

test('bulk checklist retains selections and displays validation feedback', function (string $role) {
    session()->flashInput(['siswa_ids' => ['1'], 'perilaku_id' => '3', 'tanggal' => '2026-09-21']);
    $errors = new ViewErrorBag;
    $errors->put('default', new MessageBag(['tanggal' => 'Tanggal tidak valid.']));
    view()->share('errors', $errors);

    $html = view($role.'.perilaku.bulk-bermasalah', perilakuViewData())->render();
    $xpath = perilakuDom($html);
    expect($xpath->query('//input[@name="siswa_ids[]" and @value="1" and @checked]')->length)->toBe(1);
    expect($xpath->query('//select[@name="perilaku_id"]/option[@value="3" and @selected]')->length)->toBe(1);
    expect($xpath->query('//input[@name="tanggal" and @aria-invalid="true"]')->length)->toBe(1);
    expect($html)->toContain('Tanggal tidak valid.');
})->with(['guru_mapel', 'wali_kelas']);

test('empty classes and behavior types explain why entry is unavailable', function (string $role) {
    $data = perilakuViewData();
    $data['kelasList'] = collect();
    $data['perilakus'] = collect();
    $html = view($role.'.perilaku.create', $data)->render();
    expect($html)->toContain('Belum ada siswa', 'Belum ada jenis perilaku');
    expect(perilakuDom($html)->query('//main//button[@type="submit" and @disabled]')->length)->toBe(1);

    $data['siswas'] = collect();
    $data['perilakuNegatif'] = collect();
    $html = view($role.'.perilaku.bulk-bermasalah', $data)->render();
    expect($html)->toContain('Belum ada siswa pada kelas ini.', 'Belum ada jenis perilaku negatif.');
    expect(perilakuDom($html)->query('//main//form[@method="POST"]//button[@type="submit" and @disabled]')->length)->toBe(1);
})->with(['guru_mapel', 'wali_kelas']);
