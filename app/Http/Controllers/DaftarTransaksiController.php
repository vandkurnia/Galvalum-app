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

        $dataNotaPembeli = NotaPembeli::with('Pembeli', 'Admin', 'PesananPembeli', 'PesananPembeli.Barang')->get()->toArray();
 
        foreach ($dataNotaPembeli as $index => $nota) {
            $totalPesanan = 0;
            foreach ($nota['pesanan_pembeli'] as $pesananPembeli)
            {
                $totalPesanan += $pesananPembeli['jumlah_pembelian'];

            }
            $dataNotaPembeli[$index]['total_pesanan'] = $totalPesanan; 
            // $nota->Pesanan->each(function ($pesanan) {
            //     $pesanan->count = $pesanan->jumlah_pembelian()->count();
            // });
        }   
        // dd($dataNotaPembeli);
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
