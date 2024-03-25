<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DaftarTransaksiController extends Controller
{
    public function index()
    {
        return view('daftar_transaksi');
    }
}
