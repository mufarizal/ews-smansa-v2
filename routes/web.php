<?php

use App\Http\Controllers\Admin\AkunGuruController;
use App\Http\Controllers\Admin\AkunSiswaController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GuruBk\DashboardController as GuruBkDashboardController;
use App\Http\Controllers\GuruBk\MonitoringController;
use App\Http\Controllers\GuruBk\PerilakuController;
use App\Http\Controllers\GuruMapel\AbsensiController;
use App\Http\Controllers\GuruMapel\DashboardController as GuruMapelDashboardController;
use App\Http\Controllers\GuruMapel\PerilakuSiswaController;
use App\Http\Controllers\GuruMapel\TugasController;
use App\Http\Controllers\GuruMapel\UjianController;
use App\Http\Controllers\Kurikulum\DashboardController as KurikulumDashboardController;
use App\Http\Controllers\Kurikulum\GuruBkKelasController;
use App\Http\Controllers\Kurikulum\GuruController;
use App\Http\Controllers\Kurikulum\GuruImportController;
use App\Http\Controllers\Kurikulum\GuruMapelKelasController;
use App\Http\Controllers\Kurikulum\JadwalController;
use App\Http\Controllers\Kurikulum\JadwalImportController;
use App\Http\Controllers\Kurikulum\KehadiranGuruController;
use App\Http\Controllers\Kurikulum\KelasController;
use App\Http\Controllers\Kurikulum\MapelController;
use App\Http\Controllers\Kurikulum\SemesterController;
use App\Http\Controllers\Kurikulum\SiswaController;
use App\Http\Controllers\Kurikulum\SiswaImportController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\TugasController as SiswaTugasController;
use App\Http\Controllers\WaliKelas\DashboardController as WaliKelasDashboardController;
use App\Http\Controllers\WaliKelas\KelasSayaController;
use App\Http\Controllers\WaliKelas\PerilakuSiswaController as WaliKelasPerilakuSiswaController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/ganti-password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('/ganti-password', [PasswordController::class, 'update'])->name('password.update');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('akun-guru', [AkunGuruController::class, 'index'])->name('akun-guru.index');
        Route::get('akun-guru/{guru}/role', [AkunGuruController::class, 'editRole'])->name('akun-guru.role.edit');
        Route::put('akun-guru/{guru}/role', [AkunGuruController::class, 'updateRole'])->name('akun-guru.role.update');
        Route::post('akun-guru/{guru}/toggle-aktif', [AkunGuruController::class, 'toggleAktif'])->name('akun-guru.toggle-aktif');
        Route::post('akun-guru/{guru}/reset-password', [AkunGuruController::class, 'resetPassword'])->name('akun-guru.reset-password');

        Route::get('akun-siswa', [AkunSiswaController::class, 'index'])->name('akun-siswa.index');
        Route::post('akun-siswa/{siswa}/toggle-aktif', [AkunSiswaController::class, 'toggleAktif'])->name('akun-siswa.toggle-aktif');
        Route::post('akun-siswa/{siswa}/reset-password', [AkunSiswaController::class, 'resetPassword'])->name('akun-siswa.reset-password');
    });

    Route::middleware('role:kurikulum')->prefix('kurikulum')->name('kurikulum.')->group(function () {
        Route::get('/dashboard', [KurikulumDashboardController::class, 'index'])->name('dashboard');

        Route::resource('semesters', SemesterController::class)->except(['show']);
        Route::post('/semesters/{semester}/aktifkan', [SemesterController::class, 'aktifkan'])->name('semesters.aktifkan');
        Route::post('semesters/{semester}/nonaktifkan', [SemesterController::class, 'nonaktifkan'])->name('semesters.nonaktifkan');

        Route::resource('mapels', MapelController::class)->except(['show']);

        Route::resource('kelas', KelasController::class)->except(['show'])->parameters(['kelas' => 'kelas']);

        Route::resource('gurus', GuruController::class)->except(['show', 'destroy']);
        Route::get('gurus-import', [GuruImportController::class, 'form'])->name('gurus.import.form');
        Route::get('gurus-import/template', [GuruImportController::class, 'template'])->name('gurus.import.template');
        Route::post('gurus-import', [GuruImportController::class, 'store'])->name('gurus.import.store');

        Route::resource('siswas', SiswaController::class)->except(['show', 'destroy']);
        Route::get('siswas-import', [SiswaImportController::class, 'form'])->name('siswas.import.form');
        Route::get('siswas-import/template', [SiswaImportController::class, 'template'])->name('siswas.import.template');
        Route::post('siswas-import', [SiswaImportController::class, 'store'])->name('siswas.import.store');

        Route::resource('guru-mapel-kelas', GuruMapelKelasController::class)->only(['index', 'create', 'store', 'destroy']);
        Route::resource('guru-bk-kelas', GuruBkKelasController::class)->only(['index', 'create', 'store', 'destroy']);

        Route::resource('jadwals', JadwalController::class)->except(['show']);
        Route::get('jadwals-import', [JadwalImportController::class, 'form'])->name('jadwals.import.form');
        Route::get('jadwals-import/template', [JadwalImportController::class, 'template'])->name('jadwals.import.template');
        Route::post('jadwals-import', [JadwalImportController::class, 'store'])->name('jadwals.import.store');

        Route::get('kehadiran-guru', [KehadiranGuruController::class, 'index'])->name('kehadiran-guru.index');
        Route::get('kehadiran-guru/{kehadiranGuru}/edit', [KehadiranGuruController::class, 'edit'])->name('kehadiran-guru.edit');
        Route::put('kehadiran-guru/{kehadiranGuru}', [KehadiranGuruController::class, 'update'])->name('kehadiran-guru.update');
    });

    Route::middleware('role:guru_bk')->prefix('guru-bk')->name('guru_bk.')->group(function () {
        Route::get('/dashboard', [GuruBkDashboardController::class, 'index'])->name('dashboard');
        Route::post('/dashboard/kehadiran', [GuruBkDashboardController::class, 'kehadiran'])->name('kehadiran');

        Route::resource('perilaku', PerilakuController::class)->except(['show']);
        Route::post('perilaku/{perilaku}/jadikan-default-aman', [PerilakuController::class, 'jadikanDefaultAman'])->name('perilaku.jadikan-default-aman');
        Route::prefix('monitoring')->name('monitoring.')->group(function () {
            Route::get('/', [MonitoringController::class, 'index'])->name('index');
            Route::get('/{kelas}', [MonitoringController::class, 'show'])->name('show');
            Route::get('/{kelas}/export', [MonitoringController::class, 'export'])->name('export');
            Route::get('/{kelas}/siswa/{siswa}', [MonitoringController::class, 'showSiswa'])->name('siswa');
            Route::post('/{kelas}/siswa/{siswa}/feedback', [MonitoringController::class, 'storeFeedback'])->name('feedback.store');
        });
    });

    Route::middleware('role:guru_mapel')->prefix('guru-mapel')->name('guru_mapel.')->group(function () {
        Route::get('/dashboard', [GuruMapelDashboardController::class, 'index'])->name('dashboard');
        Route::post('/dashboard/kehadiran', [GuruMapelDashboardController::class, 'kehadiran'])->name('kehadiran');

        Route::get('absensi', [AbsensiController::class, 'index'])->name('absensi.index');
        Route::get('absensi/{jadwal}', [AbsensiController::class, 'show'])->name('absensi.show');
        Route::post('absensi/{jadwal}', [AbsensiController::class, 'store'])->name('absensi.store');

        Route::prefix('tugas')->name('tugas.')->group(function () {
            Route::get('/', [TugasController::class, 'index'])->name('index');
            Route::get('/create', [TugasController::class, 'create'])->name('create');
            Route::post('/', [TugasController::class, 'store'])->name('store');
            Route::get('/{tugas}', [TugasController::class, 'show'])->name('show');
            Route::get('/{tugas}/edit', [TugasController::class, 'edit'])->name('edit');
            Route::put('/{tugas}', [TugasController::class, 'update'])->name('update');
            Route::delete('/{tugas}', [TugasController::class, 'destroy'])->name('destroy');
            Route::post('/{tugas}/publish', [TugasController::class, 'publish'])->name('publish');
            Route::post('/{tugas}/soal', [TugasController::class, 'soalStore'])->name('soal.store');
            Route::put('/{tugas}/soal/{soal}', [TugasController::class, 'soalUpdate'])->name('soal.update');
            Route::delete('/{tugas}/soal/{soal}', [TugasController::class, 'soalDestroy'])->name('soal.destroy');
            Route::get('/{tugas}/siswa', [TugasController::class, 'siswaIndex'])->name('siswa');
            Route::get('/{tugas}/siswa/{siswa}/nilai', [TugasController::class, 'nilaiForm'])->name('nilai.form');
            Route::post('/{tugas}/siswa/{siswa}/nilai', [TugasController::class, 'nilaiStore'])->name('nilai.store');
        });
        Route::prefix('ujian')->name('ujian.')->group(function () {
            Route::get('/', [UjianController::class, 'index'])->name('index');
            Route::get('/create', [UjianController::class, 'create'])->name('create');
            Route::post('/', [UjianController::class, 'store'])->name('store');
            Route::get('/{ujian}', [UjianController::class, 'show'])->name('show');
            Route::get('/{ujian}/edit', [UjianController::class, 'edit'])->name('edit');
            Route::put('/{ujian}', [UjianController::class, 'update'])->name('update');
            Route::delete('/{ujian}', [UjianController::class, 'destroy'])->name('destroy');
            Route::post('/{ujian}/publish', [UjianController::class, 'publish'])->name('publish');
            Route::post('/{ujian}/soal', [UjianController::class, 'soalStore'])->name('soal.store');
            Route::put('/{ujian}/soal/{soal}', [UjianController::class, 'soalUpdate'])->name('soal.update');
            Route::delete('/{ujian}/soal/{soal}', [UjianController::class, 'soalDestroy'])->name('soal.destroy');
            Route::get('/{ujian}/hasil', [UjianController::class, 'hasilIndex'])->name('hasil');
            Route::get('/{ujian}/siswa/{siswa}/nilai', [UjianController::class, 'nilaiForm'])->name('nilai.form');
            Route::post('/{ujian}/siswa/{siswa}/nilai', [UjianController::class, 'nilaiStore'])->name('nilai.store');
        });

        Route::prefix('perilaku')->name('perilaku.')->group(function () {
            Route::get('/', [PerilakuSiswaController::class, 'index'])->name('index');
            Route::get('/create', [PerilakuSiswaController::class, 'create'])->name('create');
            Route::post('/', [PerilakuSiswaController::class, 'store'])->name('store');
            Route::get('/{perilakuSiswa}/edit', [PerilakuSiswaController::class, 'edit'])->name('edit');
            Route::put('/{perilakuSiswa}', [PerilakuSiswaController::class, 'update'])->name('update');
            Route::delete('/{perilakuSiswa}', [PerilakuSiswaController::class, 'destroy'])->name('destroy');
            Route::get('/bulk-aman', [PerilakuSiswaController::class, 'bulkKelasAmanForm'])->name('bulk-aman.form');
            Route::post('/bulk-aman', [PerilakuSiswaController::class, 'bulkKelasAmanStore'])->name('bulk-aman.store');
            Route::get('/bulk-bermasalah', [PerilakuSiswaController::class, 'bulkKelasBermasalahForm'])->name('bulk-bermasalah.form');
            Route::post('/bulk-bermasalah', [PerilakuSiswaController::class, 'bulkKelasBermasalahStore'])->name('bulk-bermasalah.store');
        });
    });

    Route::middleware('role:wali_kelas')->prefix('wali-kelas')->name('wali_kelas.')->group(function () {
        Route::get('/dashboard', [WaliKelasDashboardController::class, 'index'])->name('dashboard');
        Route::post('/dashboard/kehadiran', [WaliKelasDashboardController::class, 'kehadiran'])->name('kehadiran');

        Route::prefix('perilaku')->name('perilaku.')->group(function () {
            Route::get('/', [WaliKelasPerilakuSiswaController::class, 'index'])->name('index');
            Route::get('/create', [WaliKelasPerilakuSiswaController::class, 'create'])->name('create');
            Route::post('/', [WaliKelasPerilakuSiswaController::class, 'store'])->name('store');
            Route::get('/{perilakuSiswa}/edit', [WaliKelasPerilakuSiswaController::class, 'edit'])->name('edit');
            Route::put('/{perilakuSiswa}', [WaliKelasPerilakuSiswaController::class, 'update'])->name('update');
            Route::delete('/{perilakuSiswa}', [WaliKelasPerilakuSiswaController::class, 'destroy'])->name('destroy');
            Route::get('/bulk-aman', [WaliKelasPerilakuSiswaController::class, 'bulkKelasAmanForm'])->name('bulk-aman.form');
            Route::post('/bulk-aman', [WaliKelasPerilakuSiswaController::class, 'bulkKelasAmanStore'])->name('bulk-aman.store');
            Route::get('/bulk-bermasalah', [WaliKelasPerilakuSiswaController::class, 'bulkKelasBermasalahForm'])->name('bulk-bermasalah.form');
            Route::post('/bulk-bermasalah', [WaliKelasPerilakuSiswaController::class, 'bulkKelasBermasalahStore'])->name('bulk-bermasalah.store');
        });

        Route::prefix('kelas-saya')->name('kelas-saya.')->group(function () {
            Route::get('/', [KelasSayaController::class, 'index'])->name('index');
            Route::get('/export/{kelas}', [KelasSayaController::class, 'export'])->name('export');
            Route::get('/siswa/{siswa}', [KelasSayaController::class, 'showSiswa'])->name('siswa');
            Route::post('/siswa/{siswa}/feedback', [KelasSayaController::class, 'storeFeedback'])->name('feedback.store');
        });
    });

    Route::middleware('role:siswa')->prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');

        Route::prefix('tugas')->name('tugas.')->group(function () {
            Route::get('/', [SiswaTugasController::class, 'index'])->name('index');
            Route::get('/{tugas}', [SiswaTugasController::class, 'show'])->name('show');
            Route::post('/{tugas}/soal/{soal}', [SiswaTugasController::class, 'jawabStore'])->name('jawab.store');
            Route::post('/{tugas}/submit', [SiswaTugasController::class, 'submitAkhir'])->name('submit');
        });
        Route::prefix('ujian')->name('ujian.')->group(function () {
            Route::get('/', [App\Http\Controllers\Siswa\UjianController::class, 'index'])->name('index');
            Route::get('/{ujian}', [App\Http\Controllers\Siswa\UjianController::class, 'show'])->name('show');
            Route::post('/{ujian}/mulai', [App\Http\Controllers\Siswa\UjianController::class, 'mulai'])->name('mulai');
            Route::get('/{ujian}/kerjakan', [App\Http\Controllers\Siswa\UjianController::class, 'kerjakan'])->name('kerjakan');
            Route::post('/{ujian}/soal/{soal}', [App\Http\Controllers\Siswa\UjianController::class, 'jawabStore'])->name('jawab.store');
            Route::post('/{ujian}/submit', [App\Http\Controllers\Siswa\UjianController::class, 'submitAkhir'])->name('submit');
            Route::get('/{ujian}/selesai', [App\Http\Controllers\Siswa\UjianController::class, 'selesai'])->name('selesai');
        });
    });
});
