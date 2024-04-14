<?php

namespace App\Http\Controllers;

use App\Models\NotaPembeli;
use Illuminate\Http\Request;

class DaftarTransaksiController extends Controller
{
    public function index()
    {

        $dataNotaPembeli = NotaPembeli::with('Pembeli', 'Admin')->get();
        // dd($dataNotaPembeli);
        return view('daftar_transaksi', ['dataNotaPembeli' => $dataNotaPembeli]);
    }
}
