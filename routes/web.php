<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\BukubesarController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\CicilanPiutangController;
use App\Http\Controllers\Cetak\ControllerInvoinceCetak;
use App\Http\Controllers\ReturController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\DaftarTransaksiController;
use App\Http\Controllers\DiskonController;
use App\Http\Controllers\JsonType\BarangJsonController;
use App\Http\Controllers\JsonType\PembeliJsonController;
use App\Http\Controllers\JsonType\PemesananBarangJsonController;
use App\Http\Controllers\Laporan\LaporanController;
use App\Http\Controllers\PemasokBarangController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PembeliController;
use App\Http\Controllers\Retur\ReturPemasokController;
use App\Http\Controllers\Retur\ReturPembeliController;
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
    Route::get('/login', [AuthController::class, 'index']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    // Master User
    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user.index'); // Menampilkan semua user
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
        Route::post('/', [UserController::class, 'store'])->name('user.store'); // Menyimpan user baru
        Route::put('/{id}', [UserController::class, 'update'])->name('user.update'); // Mengupdate user berdasarkan ID
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('user.destroy'); // Menghapus user berdasarkan ID
    });

    // Master Tipe Barang
    Route::prefix('tipe-barang')->group(function () {
        Route::get('/', [TipeBarangController::class, 'index'])->name('tipebarang.index'); // Menampilkan semua user
        Route::get('/edit/{id}', [TipeBarangController::class, 'edit'])->name('tipebarang.edit');
        Route::post('/', [TipeBarangController::class, 'store'])->name('tipebarang.store'); // Menyimpan tipebarang baru
        Route::put('/{id}', [TipeBarangController::class, 'update'])->name('tipebarang.update'); // Mengupdate tipebarang berdasarkan ID
        Route::delete('/{id}', [TipeBarangController::class, 'destroy'])->name('tipebarang.destroy'); // Menghapus tipebarang berdasarkan ID
    });
    // Master Pemasok Barang
    Route::prefix('pemasok-barang')->group(function () {
        Route::get('/', [PemasokBarangController::class, 'index'])->name('pemasokbarang.index');
        Route::get('/edit/{id}', [PemasokBarangController::class, 'edit'])->name('pemasokbarang.edit');
        Route::post('/', [PemasokBarangController::class, 'store'])->name('pemasokbarang.store'); // Menyimpan tipebarang baru
        Route::put('/{id}', [PemasokBarangController::class, 'update'])->name('pemasokbarang.update'); // Mengupdate tipebarang berdasarkan ID
        Route::delete('/{id}', [PemasokBarangController::class, 'destroy'])->name('pemasokbarang.destroy'); // Menghapus tipebarang berdasarkan ID
    });
    // Master Pemasok Barang
    Route::prefix('pembeli')->group(function () {
        Route::get('/', [PembeliController::class, 'index'])->name('pembeli.index');
        Route::get('/edit/{id}', [PembeliController::class, 'edit'])->name('pembeli.edit');
        Route::post('/', [PembeliController::class, 'store'])->name('pembeli.store'); // Menyimpan tipebarang baru
        Route::put('/{id}', [PembeliController::class, 'update'])->name('pembeli.update'); // Mengupdate tipebarang berdasarkan ID
        Route::delete('/{id}', [PembeliController::class, 'destroy'])->name('pembeli.destroy'); // Menghapus tipebarang berdasarkan ID
    });

    // Route untuk json type api internal
    Route::prefix('jsontype')->group(function () {
        Route::get('/semuabarang', [BarangJsonController::class, 'getSemuaBarangData'])->name('json.semuabarang');
        Route::get('/semuapembeli', [PembeliJsonController::class, 'getSemuaPembeliData'])->name('json.semuapembeli');


        Route::prefix('pemesanan')->group(function () {
            Route::post('/hapus-pemesanan', [PemesananBarangJsonController::class, 'hapusPesanan'])->name('json.pesanan.hapuspesan');
            Route::post('/update-pesanan', [PemesananBarangJsonController::class, 'updatePesanan'])->name('json.pesanan.updatepesanan');
        });
    });


    Route::prefix('pemesanan')->group(function () {
        Route::post('/', [PembelianController::class, 'store'])->name('pemesanan.store');
        // Route::get('/', [PembelianController::class, 'index']);
        Route::get('/edit/{id}', [PembelianController::class, 'edit'])->name('pemesanan.edit');
        Route::put('/{id}', [PembelianController::class, 'update'])->name('pemesanan.update'); // Mengupdate tipebarang berdasarkan ID
        Route::delete('/{id}', [PembelianController::class, 'destroy'])->name('pemesanan.destroy'); // Menghapus tipebarang berdasarkan ID
    });

    Route::get('/beranda', [BerandaController::class, 'index'])->name('beranda');
    Route::prefix('stok')->group(function () {
        Route::get('/', [StokController::class, 'index'])->name('stok.index');
        Route::get('/edit/{id}', [StokController::class, 'edit'])->name('stok.edit');
        Route::post('/', [StokController::class, 'store'])->name('stok.store'); // Menyimpan tipebarang baru
        Route::put('/{id}', [StokController::class, 'update'])->name('stok.update'); // Mengupdate tipebarang berdasarkan ID
        Route::delete('/{id}', [StokController::class, 'destroy'])->name('stok.destroy'); // Menghapus tipebarang berdasarkan ID
    });

    Route::prefix('laporan_penjualan')->group(function () {
        Route::get('/', [DaftarTransaksiController::class, 'index'])->name('pemesanan.index');
        Route::get('/penjualan/{id}', [DaftarTransaksiController::class, 'penjualanPDF'])->name('pemesanan.penjualanPDF');
        Route::get('/surat-jalan/{id}', [DaftarTransaksiController::class, 'suratjalanPDF'])->name('pemesanan.suratjalanPDF');
        Route::get('/info/{id}', [DaftarTransaksiController::class, 'daftarBarangPesanan'])->name('pemesanan.infobarang');
    });
    Route::prefix('retur')->group(function () {
        Route::get('/', [ReturController::class, 'index'])->name('retur.index');
        Route::prefix('pemasok')->group(function () {
            // Route::get('/', [ReturController::class, 'index'])->name('retur.pemasok.index');
            Route::get('/add/{id_pesanan}', [ReturPemasokController::class, 'add'])->name('retur.pemasok.add');
            Route::post('/', [ReturPemasokController::class, 'store'])->name('retur.pemasok.store');
            // Route::get('/', [ReturPemasokController::class, 'index']);
            Route::get('/edit/{id_retur}', [ReturPemasokController::class, 'edit'])->name('retur.pemasok.edit');
            Route::put('/{id_retur}', [ReturPemasokController::class, 'update'])->name('retur.pemasok.update'); // Mengupdate tipebarang berdasarkan ID
            Route::delete('/{id_retur}', [ReturPemasokController::class, 'destroy'])->name('retur.pemasok.destroy'); // Menghapus tipebarang berdasarkan ID
        });
        Route::prefix('pembeli')->group(function () {

            Route::get('/add/{id_nota}', [ReturPembeliController::class, 'add'])->name('retur.pembeli.add');
            Route::post('/', [ReturPembeliController::class, 'store'])->name('retur.pembeli.store');
            // Route::get('/', [ReturPembeliController::class, 'index']);
            Route::get('/edit/{id_retur}', [ReturPembeliController::class, 'edit'])->name('retur.pembeli.edit');
            Route::put('/{id_retur}', [ReturPembeliController::class, 'update'])->name('retur.pembeli.update'); // Mengupdate tipebarang berdasarkan ID
            Route::delete('/{id_retur}', [ReturPembeliController::class, 'destroy'])->name('retur.pembeli.destroy'); // Menghapus tipebarang berdasarkan ID
        });
    });


    // Master Diskon
    Route::prefix('bukubesar')->group(function () {
        Route::get('/', [BukubesarController::class, 'index'])->name('bukubesar.index'); // Menampilkan semua user
        Route::get('/edit/{id}', [BukubesarController::class, 'edit'])->name('bukubesar.edit');
        Route::post('/', [BukubesarController::class, 'store'])->name('bukubesar.store'); // Menyimpan user baru
        Route::put('/{id}', [BukubesarController::class, 'update'])->name('bukubesar.update'); // Mengupdate user berdasarkan ID
        Route::delete('/{id}', [BukubesarController::class, 'destroy'])->name('bukubesar.destroy'); // Menghapus user berdasarkan ID
        Route::get('/filter', [BukubesarController::class, 'filter'])->name('bukubesar.filter');
    });

    Route::prefix('kategori')->group(function () {
        Route::get('/', [KategoriController::class, 'index'])->name('kategori.index'); // Menampilkan semua user
        Route::get('/edit/{id}', [KategoriController::class, 'edit'])->name('kategori.edit');
        Route::post('/', [KategoriController::class, 'simpanKategori'])->name('kategori.simpan'); // Menyimpan user baru
        Route::put('/{id}', [KategoriController::class, 'update'])->name('kategori.update'); // Mengupdate user berdasarkan ID
        Route::delete('/{id}', [KategoriController::class, 'destroyKategori'])->name('kategori.destroy'); // Menghapus user berdasarkan ID
    });




    // Master Diskon
    Route::prefix('diskon')->group(function () {
        Route::get('/', [DiskonController::class, 'index'])->name('diskon.index'); // Menampilkan semua user
        Route::get('/edit/{id}', [DiskonController::class, 'edit'])->name('diskon.edit');
        Route::post('/', [DiskonController::class, 'store'])->name('diskon.store'); // Menyimpan user baru
        Route::put('/{id}', [DiskonController::class, 'update'])->name('diskon.update'); // Mengupdate user berdasarkan ID
        Route::delete('/{id}', [DiskonController::class, 'destroy'])->name('diskon.destroy'); // Menghapus user berdasarkan ID
    });
    Route::prefix('cetak')->group(function () {
        Route::get('invoice-penjualan/{no_nota}', [ControllerInvoinceCetak::class, 'print_invoice'])->name('cetak.invoice');
        Route::get('surat-jalan/{no_nota}', [ControllerInvoinceCetak::class, 'print_suratJalan'])->name('cetak.surat-jalan');
    });



    Route::prefix('cicilanpiutang')->name('')->group(function () {
        Route::get('/{id_nota}', [CicilanPiutangController::class, 'index' ])->name('cicilan.index');
        Route::get('/edit/{id_nota}/{id_bukubesar}', [CicilanPiutangController::class, 'edit'])->name('cicilan.edit');
        Route::post('/tambah', [CicilanPiutangController::class, 'store'])->name('cicilan.store');
        Route::put('/update/{id_nota}/{id_bukubesar}', [CicilanPiutangController::class, 'update'])->name('cicilan.update'); // Mengupdate user berdasarkan ID
        Route::delete('/hapus/{id_bukubesar}/{id_nota}', [CicilanPiutangController::class, 'destroy'])->name('cicilan.destroy');
    });
    Route::prefix('laporan')->group(function () {

        Route::get('/generate-csv', [LaporanController::class, 'generateCSV'])->name('laporan.generatecsv');
        Route::get('/kas-csv', [LaporanController::class, 'kaskeluarCSV'])->name('laporan.kaskeluarcsv');

        Route::get('/generate-pdf', [LaporanController::class, 'generatePDF'])->name('laporan.generatepdf');
        Route::get('/kas-pdf', [LaporanController::class, 'kaskeluarPDF'])->name('laporan.kaskeluarpdf');

        Route::get('/omzet', [LaporanController::class, 'laporanOmzet'])->name('laporan.omzet');
        Route::get('/hutang', [LaporanController::class, 'laporanHutang'])->name('laporan.hutang');
        Route::get('/piutang', [LaporanController::class, 'laporanPiutang'])->name('laporan.piutang');

        Route::get('/kas-keluar', [LaporanController::class, 'kasKeluar'])->name('laporan.kaskeluar');
        Route::post('/simpan-kas', [LaporanController::class, 'simpanKas'])->name('laporan.simpankas');
        Route::get('/editKas/{id}', [LaporanController::class, 'editKas'])->name('laporan.editKas'); 
        Route::put('/updateKas/{id}', [LaporanController::class, 'updateKas'])->name('laporan.updatekas');
        Route::delete('/deletekas/{id}', [LaporanController::class, 'destroy'])->name('laporan.destroy');

        Route::get('/modal-tambahan', [LaporanController::class, 'modalTambahan'])->name('laporan.modaltambahan');
        Route::post('/', [LaporanController::class, 'simpanModal'])->name('laporan.simpanModal');
        Route::get('/edit/{id}', [LaporanController::class, 'editModal'])->name('laporan.editModal');
        Route::put('/{id}', [LaporanController::class, 'updateModal'])->name('laporan.updateModal');
        Route::delete('/{id}', [LaporanController::class, 'modaldestroy'])->name('laporan.modaldestroy');
        Route::get('/filtermodal', [LaporanController::class, 'filterModal'])->name('laporan.filterModal');

        Route::get('/laba-rugi', [LaporanController::class, 'labaRugi'])->name('laporan.labarugi');
        Route::get('/laba-rugi-pdf', [LaporanController::class, 'labaRugiPDF'])->name('laporan.labarugiPDF');
        Route::get('/laba-rugi-csv', [LaporanController::class, 'labaRugiCSV'])->name('laporan.labarugiCSV');
        Route::get('/filter', [LaporanController::class, 'filterKas'])->name('laporan.filterKas');
    });
});
