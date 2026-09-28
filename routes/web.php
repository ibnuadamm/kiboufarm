<?php

use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('login', [App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
Route::post('login', [App\Http\Controllers\AuthController::class, 'login'])->name('login.post');
Route::post('logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('persediaan', App\Http\Controllers\PersediaanController::class);
    Route::resource('pembelian', App\Http\Controllers\PembelianController::class);
    Route::resource('modal', App\Http\Controllers\ModalController::class)->only(['index', 'create', 'store']);
    Route::resource('kolam', App\Http\Controllers\KolamController::class);
    Route::resource('penjualan', App\Http\Controllers\PenjualanController::class);
    Route::resource('vendor', App\Http\Controllers\VendorController::class);
    Route::resource('pelanggan', App\Http\Controllers\PelangganController::class);
    Route::resource('kematian', App\Http\Controllers\KematianIkanController::class);
    Route::resource('pemberian-pakan', App\Http\Controllers\PemberianPakanController::class);
    Route::resource('biaya-operasional', App\Http\Controllers\BiayaOperasionalController::class);
    Route::resource('coa', App\Http\Controllers\CoaController::class);
    Route::get('jurnal', [App\Http\Controllers\JurnalController::class, 'index'])->name('jurnal.index');
    Route::resource('buku-besar', App\Http\Controllers\BukuBesarController::class)->only(['index']);
    Route::resource('laba-rugi', App\Http\Controllers\LabaRugiController::class)->only(['index']);
    Route::get('laporan-pendapatan-kolam', [App\Http\Controllers\LaporanPendapatanKolamController::class, 'index'])->name('laporan-pendapatan-kolam.index');
});
