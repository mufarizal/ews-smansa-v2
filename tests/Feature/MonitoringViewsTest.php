<?php

use App\Models\AiRecommendation;
use App\Models\EarlyWarningResult;
use App\Models\Kelas;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

beforeEach(function () {
    $this->withoutVite();
    $user = (new User)->forceFill(['id' => 1, 'name' => 'Nama Pengguna']);
    $user->setRelation('roles', collect(['guru_bk', 'wali_kelas', 'siswa'])->map(fn ($name) => (new Role)->forceFill(['name' => $name, 'label' => $name])));
    $this->actingAs($user);
    session()->start();
    request()->setLaravelSession(session()->driver());
    view()->share('errors', new ViewErrorBag);
});

function monitoringData(): array
{
    $kelas = (new Kelas)->forceFill(['id' => 10, 'nama' => 'X-A']);
    $siswa = (new Siswa)->forceFill(['id' => 20, 'kelas_id' => 10, 'nama' => 'Nama Siswa', 'nis' => '1001']);
    $hasilTerkini = (new EarlyWarningResult)->forceFill([
        'kategori' => 'perhatian', 'skor_akhir' => 0.6789, 'tanggal_hitung' => '2026-10-01',
        'c1_akademik' => 80, 'c2_absensi' => 90, 'c3_perilaku' => 4,
        'r3_akademik' => 0.8, 'r1_absensi' => 0.9, 'r2_perilaku' => 0.4,
        'data_tidak_lengkap' => true,
        'input_metadata' => ['akademik' => [['guru_nama' => 'Guru A', 'mapel' => 'Kimia', 'status' => 'tidak_ada_catatan']], 'absensi' => [['guru_nama' => 'Guru B', 'mapel' => 'Fisika', 'status' => 'belum_diinput']]],
    ]);
    $siswa->ews_terkini = $hasilTerkini;
    $rekomendasi = (new AiRecommendation)->forceFill(['penyebab' => ['Penyebab rahasia guru'], 'saran' => ['Saran belajar <script>alert(1)</script>'], 'provider_used' => 'provider-test']);

    return ['kelas' => $kelas, 'siswa' => $siswa, 'kelasList' => collect([$kelas]), 'kelasTerpilih' => $kelas,
        'siswas' => collect([$siswa]), 'hasilTerkini' => $hasilTerkini, 'rekomendasi' => $rekomendasi,
        'trend' => collect([$hasilTerkini]), 'riwayatPerilaku' => collect(),
        'ringkasanPerKelas' => [10 => ['aman' => 2, 'perhatian' => 1, 'binaan' => 0, 'belum_ada_data' => 3]],
    ];
}

function monitoringDom(string $html): DOMXPath
{
    $document = new DOMDocument;
    @$document->loadHTML('<?xml encoding="UTF-8">'.$html);

    return new DOMXPath($document);
}

test('monitoring views render both populated and empty states', function (string $view, bool $empty) {
    $data = monitoringData();
    if ($empty) {
        foreach (['kelasList', 'siswas', 'trend', 'riwayatPerilaku'] as $key) {
            $data[$key] = collect();
        }
        foreach (['hasilTerkini', 'rekomendasi', 'kelasTerpilih'] as $key) {
            $data[$key] = null;
        }
    }
    $html = view($view, $data)->render();
    expect($html)->toContain('ews-crud')->not->toContain('<script>alert(1)</script>');
    if ($empty) {
        expect($html)->toContain('Belum ada');
    }
})->with(['guru_bk.monitoring.index', 'guru_bk.monitoring.show', 'guru_bk.monitoring.show_siswa', 'wali_kelas.kelas_saya.index', 'wali_kelas.kelas_saya.show_siswa', 'siswa.dashboard'])->with([true, false]);

test('student reflection never exposes causes or feedback and preserves greeting', function () {
    $html = view('siswa.dashboard', monitoringData())->render();
    expect($html)->toContain('Selamat datang, Nama Pengguna.', 'Saran belajar', 'Ada beberapa hal yang bisa diperbaiki');
    expect($html)->not->toContain('Penyebab rahasia guru', 'name="catatan"', 'provider-test');
});

test('teacher details support actual model fields and metadata with correct feedback routes', function (string $role) {
    $data = monitoringData();
    $bk = $role === 'guru_bk';
    if (! $bk) {
        unset($data['kelas']); // Wali controller does not pass this variable.
    }
    session()->flashInput(['catatan' => 'Masukan tersimpan']);
    $errors = new ViewErrorBag;
    $errors->put('default', new MessageBag(['catatan' => 'Periksa catatan.']));
    view()->share('errors', $errors);
    $html = view($role.($bk ? '.monitoring' : '.kelas_saya').'.show_siswa', $data)->render();
    expect($html)->toContain('0,8000', '0,9000', '0,4000', 'Guru A', 'Guru B', 'Belum diinput', 'Masukan tersimpan', 'Periksa catatan.');
    $url = $bk ? route('guru_bk.monitoring.feedback.store', [10, 20]) : route('wali_kelas.kelas-saya.feedback.store', 20);
    $xpath = monitoringDom($html);
    expect($xpath->query('//main//form[@method="POST"]')->item(0)->getAttribute('action'))->toBe($url);
    expect($xpath->query('//main//form//input[@name="_token"]')->length)->toBe(1);
    expect($html)->toContain('chart.umd.min.js', 'Data tren skor');
})->with(['guru_bk', 'wali_kelas']);

test('student tables preserve backend order, null scores and exact category shades', function (string $view) {
    $data = monitoringData();
    $data['siswas'] = collect(['binaan', 'perhatian', 'aman', null])->map(function ($kategori, $index) {
        $siswa = (new Siswa)->forceFill(['id' => 20 + $index, 'nama' => 'Urutan-'.$index, 'nis' => '100'.$index]);
        $siswa->ews_terkini = $kategori ? (new EarlyWarningResult)->forceFill(['kategori' => $kategori, 'skor_akhir' => 0, 'data_tidak_lengkap' => false]) : null;

        return $siswa;
    });
    $html = view($view, $data)->render();
    expect($html)->toContain('bg-green-100 text-green-700', 'bg-yellow-100 text-yellow-700', 'bg-red-100 text-red-700', 'bg-gray-100 text-gray-700', 'Belum ada data', '0,00');
    expect(strpos($html, 'Urutan-0'))->toBeLessThan(strpos($html, 'Urutan-1'));
    expect(strpos($html, 'Urutan-1'))->toBeLessThan(strpos($html, 'Urutan-2'));
    expect(strpos($html, 'Urutan-2'))->toBeLessThan(strpos($html, 'Urutan-3'));
})->with(['guru_bk.monitoring.show', 'wali_kelas.kelas_saya.index']);

test('wali class picker only appears for multiple classes and exports the selected class', function () {
    $data = monitoringData();
    $html = view('wali_kelas.kelas_saya.index', $data)->render();
    expect($html)->not->toContain('name="kelas_id"');
    $data['kelasList']->push((new Kelas)->forceFill(['id' => 11, 'nama' => 'X-B']));
    $html = view('wali_kelas.kelas_saya.index', $data)->render();
    $xpath = monitoringDom($html);
    expect($xpath->query('//main//form[@method="GET"]//select[@name="kelas_id"]')->length)->toBe(1);
    expect($html)->toContain(route('wali_kelas.kelas-saya.export', 10), route('wali_kelas.kelas-saya.siswa', 20));
    $data['kelasTerpilih'] = null;
    expect(view('wali_kelas.kelas_saya.index', $data)->render())->not->toContain('Export Excel');
});
