<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Panel\AntrianController as PanelAntrian;
use App\Http\Controllers\Panel\DasborController;
use App\Http\Controllers\Panel\DokterController;
use App\Http\Controllers\Panel\FaqController;
use App\Http\Controllers\Panel\JadwalController;
use App\Http\Controllers\Panel\LayananController;
use App\Http\Controllers\Panel\LogController;
use App\Http\Controllers\Panel\PengaturanController;
use App\Http\Controllers\Panel\PenggunaController;
use App\Http\Controllers\Panel\PengumumanController;
use App\Http\Controllers\Panel\PoliController;
use App\Http\Controllers\Publik\AntrianController;
use App\Http\Controllers\Publik\BerandaController;
use App\Http\Controllers\Publik\HalamanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Situs Publik
|--------------------------------------------------------------------------
*/
Route::get('/', [BerandaController::class, 'index'])->name('beranda');

Route::get('/layanan', [HalamanController::class, 'layanan'])->name('layanan.index');
Route::get('/layanan/{layanan}', [HalamanController::class, 'layananDetail'])->name('layanan.detail');
Route::get('/jadwal', [HalamanController::class, 'jadwal'])->name('jadwal');
Route::get('/alur', [HalamanController::class, 'alur'])->name('alur');
Route::get('/pengumuman', [HalamanController::class, 'pengumuman'])->name('pengumuman.index');
Route::get('/pengumuman/{pengumuman}', [HalamanController::class, 'pengumumanDetail'])->name('pengumuman.detail');
Route::get('/faq', [HalamanController::class, 'faq'])->name('faq');
Route::get('/kontak', [HalamanController::class, 'kontak'])->name('kontak');

// Antrian online
Route::get('/antrian', [AntrianController::class, 'form'])->name('antrian.form');
Route::post('/antrian', [AntrianController::class, 'simpan'])->name('antrian.simpan');
Route::get('/antrian/tiket/{kode}', [AntrianController::class, 'tiket'])->name('antrian.tiket');
Route::get('/antrian/layar', [AntrianController::class, 'layar'])->name('antrian.layar');
Route::get('/antrian/status', [AntrianController::class, 'statusJson'])->name('antrian.status');

/*
|--------------------------------------------------------------------------
| Autentikasi
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [LoginController::class, 'form'])->name('login');
    Route::post('/masuk', [LoginController::class, 'masuk'])->name('login.proses');
});

Route::post('/keluar', [LoginController::class, 'keluar'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Panel Pengelola
|--------------------------------------------------------------------------
*/
Route::prefix('panel')->name('panel.')->middleware(['auth', 'aktif'])->group(function () {
    Route::get('/', [DasborController::class, 'index'])->name('dasbor');

    // Antrian
    Route::get('/antrian', [PanelAntrian::class, 'index'])->name('antrian.index');
    Route::post('/antrian', [PanelAntrian::class, 'store'])->name('antrian.store');
    Route::post('/antrian/{antrian}/panggil', [PanelAntrian::class, 'panggil'])->name('antrian.panggil');
    Route::post('/antrian/{antrian}/selesai', [PanelAntrian::class, 'selesai'])->name('antrian.selesai');
    Route::post('/antrian/{antrian}/lewat', [PanelAntrian::class, 'lewat'])->name('antrian.lewat');
    Route::post('/antrian/{antrian}/batal', [PanelAntrian::class, 'batal'])->name('antrian.batal');
    Route::post('/antrian/panggil-berikutnya', [PanelAntrian::class, 'panggilBerikutnya'])->name('antrian.berikutnya');

    // Konten
    Route::resource('poli', PoliController::class)->except(['show']);
    Route::resource('layanan', LayananController::class)->except(['show']);
    Route::resource('dokter', DokterController::class)->except(['show']);
    Route::resource('jadwal', JadwalController::class)->except(['show']);
    Route::resource('pengumuman', PengumumanController::class)->except(['show']);
    Route::resource('faq', FaqController::class)->except(['show']);

    // Pengaturan
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::put('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');

    // Khusus admin
    Route::middleware('admin')->group(function () {
        Route::resource('pengguna', PenggunaController::class)->except(['show']);
        Route::get('/log', [LogController::class, 'index'])->name('log.index');
    });
});
