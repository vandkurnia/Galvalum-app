<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function laporanOmzet()
    {
        // Logika untuk mengambil data dan menampilkan laporan omzet
        return view('laporan.laporanomzet');
    }

    public function laporanHutang()
    {
        // Logika untuk mengambil data dan menampilkan laporan hutang
        return view('laporan.laporanhutang');
    }

    public function laporanPiutang()
    {
        // Logika untuk mengambil data dan menampilkan laporan piutang
        return view('laporan.laporanpiutang');
    }

    public function kasKeluar()
    {
        // Logika untuk mengambil data dan menampilkan laporan kas keluar
        return view('laporan.kaskeluar');
    }

    public function modalTambahan()
    {
        // Logika untuk mengambil data dan menampilkan laporan modal tambahan
        return view('laporan.modal_tambahan');
    }

    public function labaRugi()
    {
        // Logika untuk mengambil data dan menampilkan laporan laba rugi
        return view('laporan.laba_rugi');
    }
}
