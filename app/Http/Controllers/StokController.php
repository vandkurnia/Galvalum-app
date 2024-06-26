<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\RiwayatHutangModel;
use App\Models\BukubesarModel;
use App\Models\Log\LogStokBarangModel;
use App\Models\PemasokBarang;
use App\Models\StokBarangHistoryModel;
use App\Models\StokBarangModel;
use App\Models\TipeBarang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StokController extends Controller
{
    public function index()
    {
        $dataSemuaBarang = Barang::with('pemasok', 'tipeBarang', 'stokBarang')->get();
        $dataBaruSemuaBarang = [];
        foreach ($dataSemuaBarang as $barang) {
            // $totalStok = $barang->stokBarang->sum('stok_masuk') - $barang->stokBarang->sum('stok_keluar');
            // $barang->stok = $totalStok;
            $dataBaruSemuaBarang[] = $barang;
        }


        $dataTipeBarang = TipeBarang::all();
        $dataPemasok = PemasokBarang::all();

        $lastId = Barang::max('id_barang');
        $lastId = $lastId ? $lastId : 0; // handle jika tabel kosong
        $lastId++;


        $kode_barang = 'BRG' . date('Y') . date('mdHis') . str_pad($lastId, 4, '0', STR_PAD_LEFT);
        return view('stokbarang.index', ['dataSemuaBarang' => $dataBaruSemuaBarang, 'dataPemasok' => $dataPemasok, 'dataTipeBarang' => $dataTipeBarang, 'kodeBarang' => $kode_barang]);

        // return view('stokbarang.index');
    }
    public function edit(Request $request, $id)
    {
        $dataBarang = Barang::where('hash_id_barang', $id)->first();

        if ($dataBarang) {
            // Mengambil jumlah total stok barang
            // $stokbarang = StokBarangModel::where('id_barang', $dataBarang->id_barang)
            //     ->selectRaw('SUM(stok_masuk - stok_keluar) as stok')
            //     ->whereNull('deleted_at')
            //     ->first();

            // Menambahkan jumlah stok ke dalam data barang
            // $dataBarang->stok = $stokbarang->stok;

            // $stokbarang = StokBarangModel::where('id_barang', $dataBarang->id_barang)->first();
            // $dataBarang->stokoriginal = $stokbarang->stok_masuk;
            $dataBarang->stokoriginal = $dataBarang->stok;
        }
        $dataTipeBarang = TipeBarang::all();
        $dataPemasok = PemasokBarang::all();
        if (!$dataBarang) {
            return response()->json([
                'code' => 404,
                'message' => 'Not found',
                'data' => null
            ], 404);
        }
        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => view('stokbarang.edit', compact('dataBarang', 'dataTipeBarang', 'dataPemasok'))->render()
        ], 200);
    }
    public function store(Request $request)
    {

        $request->validate([
            'kode_barang' => 'required|unique:barangs,kode_barang',
            'nama_barang' => 'required',
            'harga_barang' => 'required',
            'harga_barang_pemasok' => 'required',
            'stok' => 'required',
            'ukuran' => 'required',
            // 'id_pemasok' => 'required',
            'id_tipe_barang' => 'required',
        ]);






        DB::beginTransaction();
        // Hitung total hutang barang



        // Total Kredit dari Stok Barang
        // Buat record baru untuk Barang
        $barang = new Barang();
        $barang->kode_barang = $request->kode_barang;
        $barang->nama_barang = $request->nama_barang;
        $barang->harga_barang =  $request->harga_barang;
        $barang->harga_barang_pemasok =  $request->get('harga_barang_pemasok');
        // $barang->stok = $request->stok;
        $barang->ukuran = $request->ukuran;
        $barang->id_pemasok = $request->id_pemasok;
        $barang->id_tipe_barang = $request->id_tipe_barang;
        $barang->total = $barang->harga_barang_pemasok * $request->stok;
        $barang->nominal_terbayar =  $request->get('nominal_terbayar');
        $barang->tenggat_bayar = $request->get('tenggat_bayar');
        $barang->stok = $request->stok;



        $barang->save();

        // Periksa kondisi untuk tanggal penyelesaian
        if ($barang->nominal_terbayar == $barang->total && is_null($barang->tanggal_penyelesaian)) {
            $barang->tanggal_penyelesaian = $barang->updated_at;  // Atau $barang->updated_at jika diperlukan
            $barang->save();
        } elseif ($barang->nominal_terbayar != $barang->total && !is_null($barang->tanggal_penyelesaian)) {
            $barang->tanggal_penyelesaian = null;
            $barang->save();
        }

        // Buat record baru untuk BukuBesar
        // $bukuBesar = new BukubesarModel();

        // $bukuBesar->id_akunbayar = 1; // Isi dengan nilai id_akunbayar yang sesuai
        // $bukuBesar->tanggal = date('Y-m-d'); // Isi dengan tanggal yang sesuai
        // $bukuBesar->kategori = "barang"; // Isi dengan kategori yang sesuai
        // $bukuBesar->keterangan = 'STOK BARANG ' . $barang->id_barang . ' STOK- ' . $request->stok; // Isi dengan keterangan yang sesuai
        // $bukuBesar->debit = $request->stok * $request->harga_barang_pemasok; // Isi dengan nilai kredit yang sesuai
        // $bukuBesar->save();

        // $bukuBesar = new BukubesarModel();

        // $bukuBesar->id_akunbayar = 1; // Isi dengan nilai id_akunbayar yang sesuai
        // $bukuBesar->tanggal = date('Y-m-d'); // Isi dengan tanggal yang sesuai
        // $bukuBesar->kategori = "barang"; // Isi dengan kategori yang sesuai
        // $bukuBesar->keterangan = 'STOK BARANG ' . $barang->id_barang . ' STOK- ' . $request->stok; // Isi dengan keterangan yang sesuai
        // $bukuBesar->debit = $request->stok * $request->harga_barang_pemasok; // Isi dengan nilai kredit yang sesuai
        // $bukuBesar->save();
        $bukuBesar = new BukubesarModel();

        $bukuBesar->id_akunbayar = 1; // Isi dengan nilai id_akunbayar yang sesuai
        $bukuBesar->tanggal = date('Y-m-d'); // Isi dengan tanggal yang sesuai
        $bukuBesar->kategori = "barang"; // Isi dengan kategori yang sesuai
        $bukuBesar->keterangan = "Tambah Stok Barang " . $barang->nama_barang; // Isi dengan keterangan yang sesuai
        // $bukuBesar->keterangan = 'STOK BARANG ' . $barang->id_barang . ' STOK- ' . $request->stok; // Isi dengan keterangan yang sesuai
        $bukuBesar->debit = $barang->nominal_terbayar; // Isi dengan nilai kredit yang sesuai
        $bukuBesar->save();


        // Update id bukubesar;
        $barang->id_bukubesar = $bukuBesar->id_bukubesar;

        $barang->save();


        // Buat instance dari model
        $stokbarangHistory = new StokBarangHistoryModel();
        $stokbarangHistory->id_barang = $barang->id_barang;
        $stokbarangHistory->stok_masuk = $request->stok;
        $stokbarangHistory->stok_terkini = $barang->stok;
        $stokbarangHistory->save();

        // $stokBarang = StokBarangModel::create([
        //     'stok_masuk' => $request->stok,
        //     'id_barang' => $barang->id_barang,
        //     'tipe_stok' => 'stokbarang'
        // ]);






        // $bukubesarBarang = new RiwayatHutangModel();
        // $bukubesarBarang->id_barang = $barang->id_barang;
        // $bukubesarBarang->id_bukubesar = $bukuBesar->id_bukubesar;


        // $bukubesarBarang->save();



        // Simpan ke log
        $logStokBarang = new LogStokBarangModel();
        $logStokBarang->json_content = [
            'type' => 'stok_store',
            'data' => []
        ]; // Sesuaikan dengan isi json_content Anda // Sesuaikan dengan isi json_content Anda
        $logStokBarang->tipe_log = 'barang_create';
        $logStokBarang->keterangan = 'Tambah barang ke stok dengan total stok awal ' . $request->stok;
        $logStokBarang->id_admin = Auth::user()->id_admin; // Sesuaikan dengan id_admin yang ada
        // $logStokBarang->id_stok_barang = $stokBarang->id; // Sesuaikan dengan id_stok_barang yang ada
        $logStokBarang->id_barang = $barang->id_barang; // Sesuaikan dengan id_barang yang ada
        $logStokBarang->id_stok_barang_history = $stokbarangHistory->id_stok;
        $logStokBarang->save();
        DB::commit();


        return redirect()->route('stok.index')->with('success', 'Data  Barang Berhasil disimpan');
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            // 'id_pemasok' => 'nullable|exists:pemasok,id_pemasok',
            'kode_barang' => 'required|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'ukuran' => 'required|string|max:255',
            // 'id_tipe_barang' => 'required|exists:tipe_barang,id_tipe_barang',
            'stok' => 'required|min:0',
            'harga_barang' => 'required|numeric|min:0',
            'harga_barang_pemasok' => 'required|numeric|min:0',
            'status_pembelian' => 'required|in:lunas,hutang',
            'nominal_terbayar' => 'nullable|numeric|min:0',
            'tenggat_bayar' => 'nullable|date',

        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        // Mencari barang berdasarkan hash_id_barang
        $barang = Barang::with('bukuBesar')->where('hash_id_barang', $id)->first();

        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ada');
        }

        // Update data barang
        $barang->id_pemasok = $request->id_pemasok;
        $barang->kode_barang = $request->kode_barang;
        $barang->nama_barang = $request->nama_barang;
        $barang->ukuran = $request->ukuran;
        $barang->id_tipe_barang = $request->id_tipe_barang;
        $barang->harga_barang = $request->harga_barang;
        $barang->harga_barang_pemasok = $request->harga_barang_pemasok;
        $total_lama = $barang->total;
        $nominal_terbayar_lama =  $barang->nominal_terbayar;
        $barang->nominal_terbayar = $request->nominal_terbayar;
        $barang->tenggat_bayar = $request->tenggat_bayar;
        // $barang->stok = $request->stok;
        $barang->save();


        // dd([
        //     'nominal_terbayar_lama' => $nominal_terbayar_lama,
        //     'nominal_terbayar_baru' => $request->nominal_terbayar,
        //     'total' => $total_lama
        // ]);




        // Menghitung total stok
        // $stokBarang = StokBarangModel::where('id_barang', $barang->id_barang)
        //     ->selectRaw('SUM(stok_masuk - stok_keluar) as stok')
        //     ->first();

        // Stok Lama 
        // $stokLama = $stokBarang->stok;
        $stokLama = $barang->stok;

        $stokRequest = $request->stok;


        if ($stokRequest != $stokLama) {


            $selisihStokReqdanAsli = $stokRequest  - $stokLama;



            // $stokBaru = $stokBarangubahStok->stok_masuk;
            $stokBaru = $stokLama + $selisihStokReqdanAsli;

            // Stok Baru




            // Update total barang setelah mengubah stok masuk
            $updatebarangtotal = Barang::find($barang->id_barang);
            $updatebarangtotal->stok = $stokRequest;
            $updatebarangtotal->total = $stokBaru * $updatebarangtotal->harga_barang_pemasok;
            $updatebarangtotal->save();



            // Buat instance dari model
            $stokbarangHistory = new StokBarangHistoryModel();
            $stokbarangHistory->id_barang = $barang->id_barang;
            if ($selisihStokReqdanAsli < 0) {
                $stokbarangHistory->stok_keluar = abs($selisihStokReqdanAsli); // Convert to positive and assign to stok_keluar
            } else {
                $stokbarangHistory->stok_masuk = $selisihStokReqdanAsli; // Assign to stok_masuk
            }
            $stokbarangHistory->stok_terkini = $updatebarangtotal->stok;
            $stokbarangHistory->save();




            // Simpan ke log
            $logStokBarang = new LogStokBarangModel();
            $logStokBarang->json_content = [
                'type' => 'stok_update',
                'data' => [
                    'stok_lama' => $stokLama,
                    'stok_baru' => $stokBaru,

                ]
            ]; // Sesuaikan dengan isi json_content Anda // Sesuaikan dengan isi json_content Anda // Sesuaikan dengan isi json_content Anda
            $logStokBarang->tipe_log = 'barang_update';
            $logStokBarang->keterangan = 'Update barang dari ' . $stokLama . ' ke ' . $stokBaru;
            $logStokBarang->id_admin = Auth::user()->id_admin; // Sesuaikan dengan id_admin yang ada
            // $logStokBarang->id_stok_barang = $stokBarangubahStok->id; // Sesuaikan dengan id_stok_barang yang ada
            $logStokBarang->id_barang = $updatebarangtotal->id_barang; // Sesuaikan dengan id_barang yang ada
            $logStokBarang->id_stok_barang_history = $stokbarangHistory->id_stok;
            $logStokBarang->save();
        }


        // Menggunakan DB::transaction untuk menjaga integritas transaksi
        // $bukubesarBarang = $barang->bukuBesar;
        // dd([
        //     'nominal_terbayar' => $barang->nominal_terbayar,
        //     'request nominal terbayar' => $request->nominal_terbayar,
        //     'status' => $nominal_terbayar_lama != $request->nominal_terbayar,
        //     'nominal_terbayar_lama' =>  $nominal_terbayar_lama
        // ]);

        // dd(['test' => $total_lama == $nominal_terbayar_lama]);

        // Menghitung lagi untuk konversi status pembayaran
        if ($total_lama == $nominal_terbayar_lama) {
            $barangTerbaru =  Barang::find($barang->id_barang);
            $total_baru = $barangTerbaru->total;
            $nominal_terbayar_baru = $barangTerbaru->nominal_terbayar;

            // Lunas ke lunas
            if ($total_baru == $nominal_terbayar_baru) {




                // $bukubesarbarang = RiwayatHutangModel::where('id_barang', $barang->id_barang)->first();

                // $bukubesar = BukubesarModel::withTrashed()->find($bukubesarbarang->id_bukubesar);

                // $stokBarangpertama = StokBarangModel::where('id_barang', $barang->id_barang)->first();


                $barangData = Barang::find($barang->id_barang);
                // Selisih antara debit pertama dengan 
                // $bukubesar->debit = $barangTerbaru->harga_pemasok * $barangData->stok;
                // $bukubesar->save();

                // if ($bukubesar && $bukubesar->debit > 0) {
                //     $bukubesar->restore();
                // }
                $barangData->nominal_terbayar = $barangData->total;
                $barangData->save();


                // Periksa kondisi untuk tanggal penyelesaian
                if ($barangData->nominal_terbayar == $barangData->total && is_null($barangData->tanggal_penyelesaian)) {
                    $barangData->tanggal_penyelesaian = $barangData->updated_at;  // Atau $barangData->updated_at jika diperlukan
                    $barangData->save();
                } elseif ($barangData->nominal_terbayar != $barangData->total && !is_null($barangData->tanggal_penyelesaian)) {
                    $barangData->tanggal_penyelesaian = null;
                    $barangData->save();
                }
            }

            // Lunas ke hutang
            else {

                $barangData = Barang::find($barang->id_barang);
                $barangData->nominal_terbayar =  $request->nominal_terbayar;

                $barangData->save();

                // Periksa kondisi untuk tanggal penyelesaian
                if ($barangData->nominal_terbayar == $barangData->total && is_null($barangData->tanggal_penyelesaian)) {
                    $barangData->tanggal_penyelesaian = $barangData->updated_at;  // Atau $barangData->updated_at jika diperlukan
                    $barangData->save();
                } elseif ($barangData->nominal_terbayar != $barangData->total && !is_null($barangData->tanggal_penyelesaian)) {
                    $barangData->tanggal_penyelesaian = null;
                    $barangData->save();
                }



                // Delete all records where 'id_barang' matches $barang->id_barang
                RiwayatHutangModel::where('id_barang', $barang->id_barang)->delete();




                // $barangTerbaru =  Barang::find($barang->id_barang);
                // $bukubesarbarang = RiwayatHutangModel::where('id_barang', $barang->id_barang)->first();
                // $bukubesarUpdate = BukubesarModel::find($bukubesarbarang->id_bukubesar);

                // // Selisih antara debit pertama dengan 
                // $bukubesarUpdate->debit = $barangTerbaru->nominal_terbayar;
                // $bukubesarUpdate->save();

                // // Ambil semua entri buku besar terkait dengan barang, kecuali yang pertama
                // $bukubesarBarangs = RiwayatHutangModel::where('id_barang', $barang->id_barang)
                //     ->skip(1) // Lewatkan entri pertama
                //     ->take(PHP_INT_MAX) // Ambil semua entri setelah entri pertama
                //     ->get();

                // // Hapus semua entri buku besar setelah yang pertama
                // foreach ($bukubesarBarangs as $bukubesarBarang) {
                //     $bukubesarToDelete = BukubesarModel::find($bukubesarBarang->id_bukubesar);
                //     if ($bukubesarToDelete) {
                //         $bukubesarToDelete->forceDelete();
                //     }
                // }
            }
        } else {

            $cekTotal =  Barang::find($barang->id_barang);
            $total_baru = $cekTotal->total;
            $nominal_terbayar_baru = $cekTotal->nominal_terbayar;
            // Hutang ke lunas 
            if ($total_baru == $nominal_terbayar_baru) {

                $barangData =  Barang::find($barang->id_barang);
                $barangData->nominal_terbayar = $barangData->total;
                $barangData->save();



                // Periksa kondisi untuk tanggal penyelesaian
                if ($barangData->nominal_terbayar == $barangData->total && is_null($barangData->tanggal_penyelesaian)) {
                    $barangData->tanggal_penyelesaian = $barangData->updated_at;  // Atau $barangData->updated_at jika diperlukan
                    $barangData->save();
                } elseif ($barangData->nominal_terbayar != $barangData->total && !is_null($barangData->tanggal_penyelesaian)) {
                    $barangData->tanggal_penyelesaian = null;
                    $barangData->save();
                }


                RiwayatHutangModel::where('id_barang', $barang->id_barang)->delete();
                //   $barangTer





                // $barangTerbaru =  Barang::find($barang->id_barang);
                // $bukubesarbarang = RiwayatHutangModel::where('id_barang', $barang->id_barang)->first();
                // $bukubesarUpdate = BukubesarModel::find($bukubesarbarang->id_bukubesar);

                // // Selisih antara debit pertama dengan 
                // $bukubesarUpdate->debit = $barangTerbaru->nominal_terbayar;
                // $bukubesarUpdate->save();


                // // $bukuBesarIkut = BukubesarModel::find($bukubesarUpdate->id_bukubesar);


                // // dd([
                // //     'barang' => $bukubesarUpdate,
                // //     'kucing' => $barangTerbaru,
                // //     'test' => $bukuBesarIkut
                // // ]);


                // // Ambil semua entri buku besar terkait dengan barang, kecuali yang pertama
                // $bukubesarBarangs = RiwayatHutangModel::where('id_barang', $barang->id_barang)
                //     ->skip(1) // Lewatkan entri pertama
                //     ->take(PHP_INT_MAX) // Ambil semua entri setelah entri pertama
                //     ->get();

                // // Hapus semua entri buku besar setelah yang pertama
                // foreach ($bukubesarBarangs as $bukubesarBarang) {
                //     $bukubesarToDelete = BukubesarModel::find($bukubesarBarang->id_bukubesar);
                //     if ($bukubesarToDelete) {
                //         $bukubesarToDelete->forceDelete();
                //     }
                // }
            } else if ($total_lama != $total_baru || $nominal_terbayar_lama != $request->nominal_terbayar) {
                $barangData = Barang::find($barang->id_barang);
                $barangData->nominal_terbayar =  $request->nominal_terbayar;
                $barangData->save();


                // Periksa kondisi untuk tanggal penyelesaian
                if ($barangData->nominal_terbayar == $barangData->total && is_null($barangData->tanggal_penyelesaian)) {
                    $barangData->tanggal_penyelesaian = $barangData->updated_at;  // Atau $barangData->updated_at jika diperlukan
                    $barangData->save();
                } elseif ($barangData->nominal_terbayar != $barangData->total && !is_null($barangData->tanggal_penyelesaian)) {
                    $barangData->tanggal_penyelesaian = null;
                    $barangData->save();
                }

                RiwayatHutangModel::where('id_barang', $barang->id_barang)->delete();





                // $barangTerbaru =  Barang::find($barang->id_barang);
                // $bukubesarbarang = RiwayatHutangModel::where('id_barang', $barang->id_barang)->first();
                // $bukubesarUpdate = BukubesarModel::find($bukubesarbarang->id_bukubesar);

                // // Selisih antara debit pertama dengan 
                // $bukubesarUpdate->debit = $barangTerbaru->nominal_terbayar;
                // $bukubesarUpdate->save();


                // // $bukuBesarIkut = BukubesarModel::find($bukubesarUpdate->id_bukubesar);


                // // dd([
                // //     'barang' => $bukubesarUpdate,
                // //     'kucing' => $barangTerbaru,
                // //     'test' => $bukuBesarIkut
                // // ]);


                // // Ambil semua entri buku besar terkait dengan barang, kecuali yang pertama
                // $bukubesarBarangs = RiwayatHutangModel::where('id_barang', $barang->id_barang)
                //     ->skip(1) // Lewatkan entri pertama
                //     ->take(PHP_INT_MAX) // Ambil semua entri setelah entri pertama
                //     ->get();

                // // Hapus semua entri buku besar setelah yang pertama
                // foreach ($bukubesarBarangs as $bukubesarBarang) {
                //     $bukubesarToDelete = BukubesarModel::find($bukubesarBarang->id_bukubesar);
                //     if ($bukubesarToDelete) {
                //         $bukubesarToDelete->forceDelete();
                //     }
                // }
            }
        }

        // dd("atas");
        DB::commit();


        return redirect()->route('stok.index')->with('success', 'Data barang berhasil diperbarui');
    }


    public function destroy($id)
    {
        $dataBarang = Barang::with('riwayatHutang')->where('hash_id_barang', $id)->first();
        if ($dataBarang) {

            // hapus riwayatHutang
            foreach ($dataBarang->riwayatHutang as $riwayatHutang) {
                $riwayatHutang = RiwayatHutangModel::find($riwayatHutang->id);
                $riwayatHutang->delete();
            }
            $dataBarang->delete();

            return redirect()->route('stok.index')->with('success', 'Barang berhasil dihapus');
        } else {
            return redirect()->route('stok.index')->with('error', 'Barang gagal dihapus');
        }
    }


    public function showStokBarang($id)
    {

        $dataBarang = Barang::with('stokBarang')->where('hash_id_barang', $id)->first();

        if (!$dataBarang) {
            return response()->json([
                'code' => 404,
                'message' => 'Not found',
                'data' => null
            ], 404);
        }

        $dataBarangBaru = [];
        // $totalStok = $dataBarang->stokBarang->sum('stok_masuk') - $dataBarang->stokBarang->sum('stok_keluar');
        // $dataBarang->stok = $totalStok;

        $dataBarangBaru = $dataBarang;
        // print_r($dataBarang);
        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => $dataBarangBaru
        ], 200);
    }

    public function addStock(Request $request)
    {


        $validatedData = $request->validate([
            'stok_tambah' => 'required|numeric|min:0',
            'id_barang' => 'required|exists:barangs,hash_id_barang',
            'nominal_terbayar' => 'required',

        ], [
            'stok_tambah.required' => 'Stok tambah harus diisi.',
            'stok_tambah.numeric' => 'Stok tambah harus berupa angka.',
            'stok_tambah.min' => 'Stok tambah harus lebih dari atau sama dengan 0.',
            'id_barang.exists' => 'Barang tidak ditemukan.',
        ]);

        $barang = Barang::where('hash_id_barang', $validatedData['id_barang'])->first();

        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan.');
        }

        if ($validatedData['stok_tambah'] <= 0) {
            return redirect()->back()->with('error', 'Penambahan tidak valid.');
        }

        DB::beginTransaction();
        // $barang->stok += $validatedData['stok_tambah'];

        // Kembalikan jika barang tidak ada
        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ada');
        }



        // Menghitung total stok
        // $stokBarang = StokBarangModel::where('id_barang', $barang->id_barang)->first();


        // Jumlah Stok Final Stok dari input
        // $stoktambah = $validatedData['stok_tambah'] +  $stokBarang->stok_masuk;
        // $stokBarang->stok_masuk = $stoktambah;


        // $stokBarang->save();

        // Update total
        $barang->stok += $validatedData['stok_tambah'];

        $total = $validatedData['stok_tambah'] * $barang->harga_barang_pemasok;
        $barang->total +=  $total;

        // if($request->nominal_terbayar == $total)
        // {

        // }
        // dd($request->nominal_terbayar);
        $barang->nominal_terbayar += $request->nominal_terbayar;

        $barang->save();

        // $stoktambah = $validatedData['stok_tambah'];


        // Buat instance dari model
        $stokbarangHistory = new StokBarangHistoryModel();
        $stokbarangHistory->id_barang = $barang->id_barang;
        $stokbarangHistory->stok_masuk = $validatedData['stok_tambah'];
        $stokbarangHistory->stok_terkini = $barang->stok;
        $stokbarangHistory->save();




        // Simpan ke log
        $logStokBarang = new LogStokBarangModel();
        $logStokBarang->json_content = [
            'type' => 'pembelian_store',
            'data' => []
        ]; // Sesuaikan dengan isi json_content Anda // Sesuaikan dengan isi json_content Anda
        $logStokBarang->tipe_log = 'barang_tambah_stok';
        $logStokBarang->keterangan = 'Tambah stok barang sebanyak ' . $validatedData['stok_tambah'];
        $logStokBarang->id_admin = Auth::user()->id_admin; // Sesuaikan dengan id_admin yang ada
        // $logStokBarang->id_stok_barang = $stokBarang->id; // Sesuaikan dengan id_stok_barang yang ada
        $logStokBarang->id_barang = $barang->id_barang; // Sesuaikan dengan id_barang yang ada
        $logStokBarang->id_stok_barang_history = $stokbarangHistory->id_stok;
        $logStokBarang->save();







        // Buat record baru untuk BukuBesar
        // $bukuBesar = new BukubesarModel();

        // $bukuBesar->id_akunbayar = 1; // Isi dengan nilai id_akunbayar yang sesuai
        // $bukuBesar->tanggal = date('Y-m-d'); // Isi dengan tanggal yang sesuai
        // $bukuBesar->kategori = "barang_tambah_stok"; // Isi dengan kategori yang sesuai
        // $bukuBesar->keterangan = 'TAMBAH STOK BARANG ' . $barang->id_barang . ' STOK- ' . $request->stok; // Isi dengan keterangan yang sesuai
        // $bukuBesar->debit = $request->nominal_terbayar; // Isi dengan nilai kredit yang sesuai
        // $bukuBesar->save();

        // $bukubesarBarang = new RiwayatHutangModel();
        // $bukubesarBarang->id_barang = $barang->id_barang;
        // $bukubesarBarang->id_bukubesar = $bukuBesar->id_bukubesar;
        // $bukubesarBarang->save();

        // // Hitung lagi nominal terbayar stok 
        // $barangupdated = Barang::with('bukuBesar')->find($barang->id_barang);
        // $totalNominalTerbayar = 0;
        // foreach ($barangupdated->bukuBesar as $bukuBesar) {
        //     $totalNominalTerbayar += $bukuBesar->debit;
        // }

        // $barangupdated->nominal_terbayar = $totalNominalTerbayar;
        // $barangupdated->save();


        DB::commit();

        return redirect()->route('stok.index')->with('success', 'Berhasil mengupdate stok barang.');
    }
    public function minusStok(Request $request)
    {



        $validatedData = $request->validate([
            'stok_kurang' => 'required|numeric|min:0',
            'id_barang' => 'required|exists:barangs,hash_id_barang',
            'nominal_terbayar' => 'required',

        ], [
            'stok_kurang.required' => 'Stok tambah harus diisi.',
            'stok_kurang.numeric' => 'Stok tambah harus berupa angka.',
            'stok_kurang.min' => 'Stok tambah harus lebih dari atau sama dengan 0.',
            'id_barang.exists' => 'Barang tidak ditemukan.',
        ]);

        // dd($validatedData);
        $barang = Barang::where('hash_id_barang', $validatedData['id_barang'])->first();

        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan.');
        }

        if ($validatedData['stok_kurang'] <= 0) {
            return redirect()->back()->with('error', 'Pengurangan stok tidak valid.');
        }

        DB::beginTransaction();
        // $barang->stok += $validatedData['stok_tambah'];

        // Kembalikan jika barang tidak ada
        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ada');
        }



        // Menghitung total stok
        // $stokBarang = StokBarangModel::where('id_barang', $barang->id_barang)->first();


        // Jumlah Stok Final Stok dari input
        // $stoktambah = $validatedData['stok_tambah'] +  $stokBarang->stok_masuk;
        // $stokBarang->stok_masuk = $stoktambah;


        // $stokBarang->save();

        // Update total
        $barang->stok -=   $validatedData['stok_kurang'];
        $barang->total -= $validatedData['stok_kurang'] * $barang->harga_barang_pemasok;
        $barang->nominal_terbayar -= $request->nominal_terbayar;
        $barang->save();

        // $stoktambah = $validatedData['stok_kurang'];

        // Buat instance dari model
        $stokbarangHistory = new StokBarangHistoryModel();
        $stokbarangHistory->id_barang = $barang->id_barang;
        $stokbarangHistory->stok_keluar = $validatedData['stok_kurang'];
        $stokbarangHistory->stok_terkini = $barang->stok;
        $stokbarangHistory->save();




        // Simpan ke log
        $logStokBarang = new LogStokBarangModel();
        $logStokBarang->json_content = [
            'type' => 'pembelian_stok',
            'data' => []
        ]; // Sesuaikan dengan isi json_content Anda // Sesuaikan dengan isi json_content Anda
        $logStokBarang->tipe_log = 'barang_kurang_stok';
        $logStokBarang->keterangan = 'Kurang  stok barang sebanyak ' . $validatedData['stok_kurang'];
        $logStokBarang->id_admin = Auth::user()->id_admin; // Sesuaikan dengan id_admin yang ada
        // $logStokBarang->id_stok_barang = $stokBarang->id; // Sesuaikan dengan id_stok_barang yang ada
        $logStokBarang->id_barang = $barang->id_barang; // Sesuaikan dengan id_barang yang ada
        $logStokBarang->id_stok_barang_history = $stokbarangHistory->id_stok;
        $logStokBarang->save();







        // // Buat record baru untuk BukuBesar
        // $bukuBesar = new BukubesarModel();

        // $bukuBesar->id_akunbayar = 1; // Isi dengan nilai id_akunbayar yang sesuai
        // $bukuBesar->tanggal = date('Y-m-d'); // Isi dengan tanggal yang sesuai
        // $bukuBesar->kategori = "barang_kurang_stok"; // Isi dengan kategori yang sesuai
        // $bukuBesar->keterangan = 'KURANG STOK BARANG ' . $barang->id_barang . ' STOK- ' . $request->stok; // Isi dengan keterangan yang sesuai
        // $bukuBesar->debit = $request->nominal_terbayar; // Isi dengan nilai kredit yang sesuai
        // $bukuBesar->save();

        // $bukubesarBarang = new RiwayatHutangModel();
        // $bukubesarBarang->id_barang = $barang->id_barang;
        // $bukubesarBarang->id_bukubesar = $bukuBesar->id_bukubesar;
        // $bukubesarBarang->save();

        // // Hitung lagi nominal terbayar stok 
        // $barangupdated = Barang::with('bukuBesar')->find($barang->id_barang);
        // $totalNominalTerbayar = 0;
        // foreach ($barangupdated->bukuBesar as $bukuBesar) {
        //     $totalNominalTerbayar += $bukuBesar->debit;
        // }

        // $barangupdated->nominal_terbayar = $totalNominalTerbayar;
        // $barangupdated->save();


        DB::commit();

        return redirect()->route('stok.index')->with('success', 'Berhasil mengupdate stok barang.');
    }
    // public function minusStok(Request $request)
    // {


    //     $validatedData = $request->validate([
    //         'stok_kurang' => 'required|numeric|min:0',
    //         'id_barang' => 'required|exists:barangs,hash_id_barang',
    //     ], [
    //         'stok_kurang.required' => 'Pengurangan Stok harus diisi.',
    //         'stok_kurang.numeric' => 'Pengurangan Stok harus berupa angka.',
    //         'stok_kurang.min' => 'Pengurangan Stok harus lebih dari atau sama dengan 0.',
    //         'id_barang.exists' => 'Barang tidak ditemukan.',
    //     ]);

    //     $barang = Barang::where('hash_id_barang', $validatedData['id_barang'])->first();

    //     if (!$barang) {
    //         return redirect()->back()->with('error', 'Barang tidak ditemukan.');
    //     }

    //     if ($validatedData['stok_kurang'] <= 0) {
    //         return redirect()->back()->with('error', 'Pengurangan tidak valid atau 0.');
    //     }


    //     DB::beginTransaction();
    //     // $bukuBesar = new BukubesarModel();
    //     // $bukuBesar->kategori = "barang"; // Isi dengan kategori yang sesuai
    //     // $bukuBesar->keterangan = 'STOK BARANG ' . $barang->hash_id_barang . ' STOK- ' . $request->stok; // Isi dengan keterangan yang sesuai
    //     // $bukuBesar->tanggal = date('Y-m-d');
    //     // $bukuBesar->sub_kategori = "hutang";
    //     // $bukuBesar->debit = $validatedData['stok_kurang'];
    //     // $bukuBesar->keterangan = "Pengurangan stok " . $validatedData['stok_kurang'];
    //     // $bukuBesar->save();


    //     StokBarangModel::create([
    //         'stok_keluar' => $validatedData['stok_kurang'],
    //         'id_barang' => $barang->id_barang
    //         // 'id_bukubesar' => $bukuBesar->id_bukubesar
    //     ]);



    //     // Mencari barang berdasarkan hash_id_barang
    //     $barang = Barang::find($barang->id_barang);

    //     // Kembalikan jika barang tidak ada
    //     if (!$barang) {
    //         return redirect()->back()->with('error', 'Barang tidak ada');
    //     }
    //     // Menghitung total stok
    //     $stokBarang = StokBarangModel::where('id_barang', $barang->id_barang)
    //         ->selectRaw('SUM(stok_masuk - stok_keluar) as stok')
    //         ->first();

    //     // Update total
    //     $barang->total = $stokBarang->stok * $barang->harga_barang_pemasok;

    //     $barang->save();
    //     DB::commit();

    //     return redirect()->route('stok.index')->with('success', 'Berhasil mengupdate stok barang.');
    // }
}
