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
    public function index(Request $request)
    {

        // Ambil tanggal dari query string
        $tanggal = $request->get('tanggal');

        // Buat query builder untuk model NotaPembeli
        $query = NotaPembeli::with('Pembeli', 'Admin', 'PesananPembeli', 'PesananPembeli.Barang');

        // Jika tanggal tersedia dalam query string, tambahkan kondisi WHERE
        if ($tanggal) {
            $query->whereDate('created_at', $tanggal);
        }
        // Urutkan hasil berdasarkan kolom created_at dari yang terbaru ke yang terlama
        $query->orderBy('created_at', 'DESC');

        // Ambil data sesuai dengan kondisi yang telah diterapkan
        $dataNotaPembeli = $query->get()->toArray();
        foreach ($dataNotaPembeli as $index => $nota) {
            $totalPesanan = 0;
            foreach ($nota['pesanan_pembeli'] as $pesananPembeli) {
                $totalPesanan += $pesananPembeli['jumlah_pembelian'];
            }


            if ($nota['total'] == ($nota['nominal_terbayar'] + $nota['dp'])) {
                $statusPembayaran = "Lunas";
            } else if ($nota['total'] < ($nota['nominal_terbayar'] + $nota['dp'])) {
                $statusPembayaran = "Kelebihan";
            } else if ($nota['total'] > ($nota['nominal_terbayar'] + $nota['dp'])) {
                $statusPembayaran = "Piutang";
            } else {
                $statusPembayaran = "Tidak Valid";
            }
            $dataNotaPembeli[$index]['status_pembayaran'] = $statusPembayaran;
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

        return response()->json([
            'code' => 500,
            'message' => 'This page is under construction'
        ]);
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
