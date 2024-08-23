<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\NotaPembeli;
use App\Models\PesananPembeli;
use App\Models\DiskonModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;

class DaftarTransaksiController extends Controller
{
    public function index(Request $request)
    {

        // Ambil tanggal dari query string
        $tanggal = $request->get('tanggal');
        // Check if the request is an API call
        $api = $request->input('api', 'no');
        // Get the search term from the request, default to empty string
        $searchTerm = $request->input('search.value', '');
        if ($api == 'yes') {

            // Order direction 
            
            // Start time
            $startTime = microtime(true);


            // Get the date filter from the request, default to today
            $tanggal = $request->input('dateFilter', Carbon::today()->format('Y-m-d'));

            // Create query builder for NotaPembeli with related models
            $query = NotaPembeli::with('Pembeli', 'Admin', 'PesananPembeli', 'PesananPembeli.Barang');

           

            $tanggalEnabled = true;
            // Apply search filter if provided
            if ($searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('no_nota', 'like', '%' . $searchTerm . '%')
                        ->orWhereHas('Pembeli', function ($q) use ($searchTerm) {
                            $q->where('no_hp_pembeli', 'like', '%' . $searchTerm . '%')
                                ->orWhere('nama_pembeli', 'like', '%' . $searchTerm . '%');
                        })
                        ->orWhere('metode_pembayaran', 'like', '%' . $searchTerm . '%');
                });
                $tanggalEnabled = false;
            }


             // Apply date filter if provided
             if ($tanggal && $tanggalEnabled) {
                $query->whereDate('created_at', $tanggal);
            }

            // Sort results by created_at in descending order
            $query->orderBy('created_at', 'DESC');

            // Paginate the results based on DataTable request
            $totalRecords = $query->count();
            $results = $query->offset($request->input('start'))
                ->limit($request->input('length'))
                ->get();

            // Process each NotaPembeli record
            $dataNotaPembeli = $results->toArray();
            foreach ($dataNotaPembeli as $index => $nota) {
                $totalPesanan = 0;

                // Calculate total pesanan (orders)
                foreach ($nota['pesanan_pembeli'] as $pesananPembeli) {
                    $totalPesanan += $pesananPembeli['jumlah_pembelian'];
                }

                // Determine the status of the payment
                if ($nota['total'] == ($nota['nominal_terbayar'] + $nota['dp'])) {
                    $statusPembayaran = "Lunas";
                } else if ($nota['total'] < ($nota['nominal_terbayar'] + $nota['dp'])) {
                    $statusPembayaran = "Kelebihan " . ($nota['nominal_terbayar'] + $nota['dp'] - $nota['total']);
                } else if ($nota['total'] > ($nota['nominal_terbayar'] + $nota['dp'])) {
                    $statusPembayaran = "Piutang";
                } else {
                    $statusPembayaran = "Tidak Valid";
                }



                // Prepare the action buttons dynamically
                $cetakInvoice = '<button class="btn btn-warning" onclick="print(\'' . route('cetak.invoice', ['no_nota' => $nota['no_nota']]) . '\', \'' . route('cetak.surat-jalan', ['no_nota' => $nota['no_nota']]) . '\')"><i class="fas fa-print"></i></button>';



                $actionButtons = '<a href="' . route('retur.pembeli.add', ['id_nota' => $nota['id_nota']]) . '" class="btn btn-info">Retur</a>';

                if (Auth::user()->role == 'admin') {

                    $actionButtons .= '<a href="' . route('pemesanan.edit', ['id' => $nota['id_nota']]) . '" class="btn btn-primary "><i class="fas fa-edit"></i></a>';
                    $actionButtons .= '<button class="btn btn-danger " onclick="funcHapusUser(\'' . route('pemesanan.destroy', ['id' => $nota['id_nota']]) . '\', 0)"><i class="fas fa-trash"></i></button>';


                    $logButton = '<a href="' . route('log-nota.index', ['id_nota' => $nota['id_nota']]) . '" class="btn btn-info "><i class="fas fa-info-circle"></i></a>';
                }
                // Add additional fields to the response data
                $dataNotaPembeli[$index]['status_pembayaran'] = $statusPembayaran;
                $dataNotaPembeli[$index]['total_pesanan'] = $totalPesanan;
                $dataNotaPembeli[$index]['cetak_invoice'] = $cetakInvoice;

                $dataNotaPembeli[$index]['action_buttons'] = $actionButtons;
                $dataNotaPembeli[$index]['log_button'] = $logButton;
            }


            // End time
            $endTime = microtime(true);

            // Calculate total load time
            $totalLoadTime = $endTime - $startTime;

            // Debug total load time
            debug("total Load time :" . $totalLoadTime . ' seconds');
            // Return the data in DataTable format
            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $dataNotaPembeli,
            ]);
        } else {
            $dataNotaPembeli = [];
        }



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
