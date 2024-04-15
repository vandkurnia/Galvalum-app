<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\NotaPembeli;
use App\Models\PesananPembeli;
use Illuminate\Http\Request;

class DaftarTransaksiController extends Controller
{
    public function index()
    {

        $dataNotaPembeli = NotaPembeli::with('Pembeli', 'Admin')->get();
        return view('daftar_transaksi.daftar_transaksi', ['dataNotaPembeli' => $dataNotaPembeli]);
    }
    public function daftarBarangPesanan($id_nota)
    {

        $dataBarangNotaPembeli = PesananPembeli::with('Barang')->where('id_nota', $id_nota)->get();
        if ($dataBarangNotaPembeli->isEmpty()) {
            return response()->json([
                'code' => 404,
                'message' => 'Data not found',
                'data' => null
            ]);
        }

        return response()->json([
            'code' => 200,
            'message' => 'Berhasil menampilkan data',
            'data' => view('daftar_transaksi.info', compact('dataBarangNotaPembeli'))->render()
        ]);
    }
}
