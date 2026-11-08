<?php

use App\Http\Controllers\AsetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\RiwayatAsetController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Rute khusus harus sebelum resource agar tidak tertangkap {aset}
    Route::get('aset/cetak', [AsetController::class, 'cetakDaftar'])->name('aset.cetak-daftar');
    Route::get('aset/export', [AsetController::class, 'export'])->name('aset.export');
    Route::get('aset/{aset}/cetak', [AsetController::class, 'cetak'])->name('aset.cetak');
    Route::get('aset/{aset}/nota', [AsetController::class, 'nota'])->name('aset.nota');
    Route::post('aset/{aset}/riwayat', [RiwayatAsetController::class, 'store'])->name('aset.riwayat.store');

    Route::resource('aset', AsetController::class)->parameters(['aset' => 'aset']);
    Route::resource('kategori', KategoriController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->parameters(['kategori' => 'kategori']);
});
