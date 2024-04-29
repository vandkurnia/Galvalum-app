<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\NotaPembeli;
use App\Models\PesananPembeli;
use App\Models\DiskonModel;
use Illuminate\Http\Request;
use PDF;

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

    public function penjualanPDF(Request $request, $id)
    {
        $notaPembelian = NotaPembeli::where('id_nota', $id)->with('Pembeli', 'PesananPembeli')->first();
        $dataPesanan = PesananPembeli::where('id_nota', $notaPembelian->id_nota)->with('Barang', 'Barang.TipeBarang')->get();
        $dataDiskon = DiskonModel::all();

        $pdf = PDF::loadView('pdfprint.invoice-penjualan', compact('notaPembelian', 'dataPesanan', 'dataDiskon'));

        return $pdf->download('Penjualan.pdf');
    }

    public function suratjalanPDF(Request $request, $id)
    {
        $notaPembelian = NotaPembeli::where('id_nota', $id)->with('Pembeli', 'PesananPembeli')->first();
        $dataPesanan = PesananPembeli::where('id_nota', $notaPembelian->id_nota)->with('Barang', 'Barang.TipeBarang')->get();
        $dataDiskon = DiskonModel::all();

        $pdf = PDF::loadView('pdfprint.surat-jalan', compact('notaPembelian', 'dataPesanan', 'dataDiskon'));

        return $pdf->download('Surat Jalan.pdf');
    }
}
