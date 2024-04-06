<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\ReturController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\DaftarTransaksiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PemasokBarangController;
use App\Http\Controllers\TipeBarangController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['guest'])->group(function () {
    Route::get('/', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);

    // Master User
    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index']);
    });
    // Master Tipe Barang
    Route::prefix('tipe-barang')->group(function () {
        Route::get('/', [TipeBarangController::class, 'index']);
    });
    // Master Pemasok Barang
    Route::prefix('pemasok-barang')->group(function () {
        Route::get('/', [PemasokBarangController::class, 'index']);

    });

    Route::get('/beranda', [BerandaController::class, 'index'])->name('beranda');
    Route::get('/retur', [ReturController::class, 'index']);
    Route::get('/stok', [StokController::class, 'index']);
    Route::get('/daftar_transaksi', [DaftarTransaksiController::class, 'index']);
});
