<?php

namespace App\Http\Controllers\Retur;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\RiwayatHutangModel;
use App\Models\BukubesarModel;
use App\Models\Log\LogStokBarangModel;
use App\Models\PemasokBarang;
use App\Models\PesananPembeli;
use App\Models\Retur\ReturPemasokModel;
use App\Models\StokBarangHistoryModel;
use App\Models\StokBarangModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class ReturPemasokController extends Controller
{

    private function  hashToId($hash_id)
    {

        return ReturPemasokModel::where('hash_id_retur_pemasok', $hash_id)->first();
    }
    public function edit($id_retur)
    {
        $dataReturPemasok = ReturPemasokModel::find($this->hashToId($id_retur)->id_retur_pemasok);
        $dataPemasok = PemasokBarang::all();
        return view('retur.pemasok.edit', compact('dataReturPemasok', 'dataPemasok'));
    }
    public function add($id_barang)
    {
        $dataBarang = Barang::with('stokBarang')->where('hash_id_barang', $id_barang)->first();

        if (!$dataBarang) {
            return redirect()->route('retur.index')->with('error', 'Barang tidak ditemukan');
        }

        // Calculate the stock
        // $totalStok = $dataBarang->stokBarang->sum(function ($stok) {
        //     return $stok->stok_masuk - $stok->stok_keluar;
        // });
        $totalStok = $dataBarang->stok;
        return view('retur.pemasok.add', compact('id_barang', 'dataBarang', 'totalStok'));
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tanggal_retur_pemasok' => 'required|date',
            'bukti_retur_pemasok' => 'required', // 10MB max
            'jenis_retur' => 'required|in:Rusak,Tidak Rusak',
            'retur_data' => 'required',
            // 'nominal_terbayar' => 'required'
        ]);

        $returData = json_decode($validatedData['retur_data'], true);

        DB::beginTransaction();
        try {
            foreach ($returData as $item) {
                // Validate each item
                $barang = Barang::with('Pemasok', 'stokBarang')->where('hash_id_barang', $item['id_barang'])->first();

                $total_lama = $barang->total;
                $nominal_terbayar_lama =  $barang->nominal_terbayar;

                if (!$barang) {
                    throw new Exception('Barang Tidak ada');
                }

                // Calculate stock
                // $stok = StokBarangModel::where('id_barang', $barang->id_barang)
                //     ->selectRaw('SUM(stok_masuk - stok_keluar) as stok')
                //     ->value('stok');


                if ($barang->stok < $item['qty']) {
                    throw new Exception('Stok tidak cukup untuk retur');
                }


                // Generate No Retur
                $totalIdReturPembeli = ReturPemasokModel::count();
                if ($totalIdReturPembeli === 0) {
                    $totalIdReturPembeli = 1;
                } else {
                    $totalIdReturPembeli = $totalIdReturPembeli + 1;
                }

                $NoreturPembeli = "RETURP" . date('YmdHis') . $totalIdReturPembeli;




                // Olah File Uploadan Bukti

                $fileData = json_decode($request->bukti_retur_pemasok, true);


                // Mendapatkan base64 data dari JSON
                $base64Data = $fileData['data'];
                // Decode base64 data ke file binary
                $fileBinary = base64_decode($base64Data);
                // Direktori tujuan penyimpanan file
                $targetDirectory = public_path('retur/pemasok/');
                if (!file_exists($targetDirectory)) {
                    mkdir($targetDirectory, 0777, true); // Buat direktori secara rekursif
                }
                // Tambahkan .gitignore untuk menghindari terkirimnya gambar
                $gitignoreFile = $targetDirectory . '.gitignore';
                if (!file_exists($gitignoreFile)) {
                    file_put_contents($gitignoreFile, "*\n!.gitignore"); // Isi .gitignore dengan aturan umum
                }
                // Membuat nama file yang unik (misalnya, gabungan dari id dan nama file)
                $fileName = $fileData['id'] . '_' . date('Ymdhis') . '_' . $fileData['name'];
                // Menyimpan file ke direktori tujuan
                file_put_contents($targetDirectory . $fileName, $fileBinary);
                $buktiReturPemasok = $fileName;

                // Create StokBarang record
                // $stokBarangupdatelama = StokBarangModel::find($barang->stokBarang[0]->id);
                $stokbaru = $barang->stok - $item['qty'];
                // $totalbaru = $stokBarangupdatelama->stok_masuk * $stokbaru;
                // $stokBarangupdatelama->stok_masuk = $stokbaru;
                // $stokBarangupdatelama->save();




                $barang->stok = $barang->stok - $item['qty'];
                $barang->save();
                // Buat instance dari model
                $stokbarangHistory = new StokBarangHistoryModel();
                $stokbarangHistory->id_barang = $barang->id_barang;
                // $stokbarangHistory->stok_masuk = $validatedData['stok_tambah'];
                $stokbarangHistory->stok_keluar = $item['qty'];
                $stokbarangHistory->stok_terkini = $stokbaru;
                $stokbarangHistory->save();


                // Simpan ke log
                $logStokBarang = new LogStokBarangModel();
                $logStokBarang->json_content = [
                    'type' => 'retur_pemasok_store',
                    'data' => []
                ]; // Sesuaikan dengan isi json_content Anda // Sesuaikan dengan isi json_content Anda
                $logStokBarang->tipe_log = 'retur_pemasok_create';
                $logStokBarang->keterangan = 'Tambah Retur Pemasok ';
                $logStokBarang->id_admin = Auth::user()->id_admin; // Sesuaikan dengan id_admin yang ada
                // $logStokBarang->id_stok_barang = $stokBarangupdatelama->id; // Sesuaikan dengan id_stok_barang yang ada
                $logStokBarang->id_barang = $barang->id_barang; // Sesuaikan dengan id_barang yang ada
                $logStokBarang->id_stok_barang_history =  $stokbarangHistory->id_stok;
                $logStokBarang->save();

                // Create ReturPemasok record
                $returPemasok = new ReturPemasokModel();

                // Set the attributes
                $returPemasok->no_retur_pemasok = $NoreturPembeli;
                $returPemasok->tanggal_retur = now()->format('Y-m-d');
                $returPemasok->bukti_retur_pemasok = $buktiReturPemasok; // Example file content
                $returPemasok->jenis_retur = $request->jenis_retur;
                $returPemasok->total_nilai_retur = $request->total_nilai_retur;
                $returPemasok->pengembalian_data = '0';
                $returPemasok->kekurangan = '0';
                $returPemasok->harga = $barang->harga_barang_pemasok;
                $returPemasok->total = $barang->harga_barang_pemasok * $item['qty'];
                $returPemasok->qty = $item['qty'];
                $returPemasok->qty_sebelum_perubahan = null;

                if ($validatedData['jenis_retur'] == 'Rusak') {

                    $returPemasok->type_retur_pesanan = 'retur_murni_rusak';
                } else {
                    $returPemasok->type_retur_pesanan = 'retur_murni_tidak_rusak';
                }
                $returPemasok->status = 'Selesai';
                $returPemasok->id_pemasok = $barang->id_pemasok; // Assuming id_pemasok 1 exists
                $returPemasok->id_barang = $barang->id_barang; // Assuming id_barang 1 exists
                // $returPemasok->id_stok_barang = $stokBarangupdatelama->id; // Assuming id_stok_barang 1 exists





                // Save the record to the database
                $returPemasok->save();

                // Update nominal_terbayar 
                $barangUpdate = Barang::find($barang->id_barang);
                $barangUpdate->nominal_terbayar;
                $barangUpdate->save();




                // // Menghitung jika lunas maka otomatis nominal terbayar langsung mengisi bukubesar pertama jika hutang maka hapus seluruh bukubesar lalu hitung lagi
                // if ($total_lama == $nominal_terbayar_lama) {




                //     $barangTerbaru =  Barang::find($barang->id_barang);



                //     $bukubesarbarang = BukubesarModel::where('id_barang', $barang->id_barang)->first();
                //     $bukubesar = BukubesarModel::find($bukubesarbarang->id_bukubesar);
                //     $stokBarangpertama = StokBarangModel::where('id_barang', $barang->id_barang)->first();

                //     // Selisih antara debit pertama dengan 
                //     $bukubesar->debit = $barangTerbaru->harga_pemasok * $stokBarangpertama->stok_masuk;
                //     $bukubesar->save();
                // } else {
                //     // dd([
                //     //     'test' => $nominal_terbayar_lama != $request->nominal_terbayar,
                //     //     'nominal_terbayar' => $nominal_terbayar_lama,
                //     //     'nominal_terbayar_hehe' => $request->nominal_terbayar
                //     // ]);
                //     $cekTotal =  Barang::find($barang->id_barang);
                //     // Kalau total berbeda dengan total lama maka refresh
                //     if ($total_lama !=  $cekTotal->total) {

                //         $barangTerbaru =  Barang::find($barang->id_barang);
                //         $bukubesarbarang = RiwayatHutangModel::where('id_barang', $barang->id_barang)->first();
                //         $bukubesarUpdate = BukubesarModel::find($bukubesarbarang->id_bukubesar);

                //         // Selisih antara debit pertama dengan 
                //         $bukubesarUpdate->debit = $barangTerbaru->nominal_terbayar;
                //         $bukubesarUpdate->save();


                //         // $bukuBesarIkut = BukubesarModel::find($bukubesarUpdate->id_bukubesar);


                //         // dd([
                //         //     'barang' => $bukubesarUpdate,
                //         //     'kucing' => $barangTerbaru,
                //         //     'test' => $bukuBesarIkut
                //         // ]);


                //         // Ambil semua entri buku besar terkait dengan barang, kecuali yang pertama
                //         $bukubesarBarangs = RiwayatHutangModel::where('id_barang', $barang->id_barang)
                //             ->skip(1) // Lewatkan entri pertama
                //             ->take(PHP_INT_MAX) // Ambil semua entri setelah entri pertama
                //             ->get();

                //         // Hapus semua entri buku besar setelah yang pertama
                //         foreach ($bukubesarBarangs as $bukubesarBarang) {
                //             $bukubesarToDelete = BukubesarModel::find($bukubesarBarang->id_bukubesar);
                //             if ($bukubesarToDelete) {
                //                 $bukubesarToDelete->forceDelete();
                //             }
                //         }
                //     } else if ($nominal_terbayar_lama != $request->nominal_terbayar) {
                //         $barangTerbaru =  Barang::find($barang->id_barang);
                //         $bukubesarbarang = RiwayatHutangModel::where('id_barang', $barang->id_barang)->first();
                //         $bukubesarUpdate = BukubesarModel::find($bukubesarbarang->id_bukubesar);

                //         // Selisih antara debit pertama dengan 
                //         $bukubesarUpdate->debit = $barangTerbaru->nominal_terbayar;
                //         $bukubesarUpdate->save();


                //         // $bukuBesarIkut = BukubesarModel::find($bukubesarUpdate->id_bukubesar);


                //         // dd([
                //         //     'barang' => $bukubesarUpdate,
                //         //     'kucing' => $barangTerbaru,
                //         //     'test' => $bukuBesarIkut
                //         // ]);


                //         // Ambil semua entri buku besar terkait dengan barang, kecuali yang pertama
                //         $bukubesarBarangs = RiwayatHutangModel::where('id_barang', $barang->id_barang)
                //             ->skip(1) // Lewatkan entri pertama
                //             ->take(PHP_INT_MAX) // Ambil semua entri setelah entri pertama
                //             ->get();

                //         // Hapus semua entri buku besar setelah yang pertama
                //         foreach ($bukubesarBarangs as $bukubesarBarang) {
                //             $bukubesarToDelete = BukubesarModel::find($bukubesarBarang->id_bukubesar);
                //             if ($bukubesarToDelete) {
                //                 $bukubesarToDelete->forceDelete();
                //             }
                //         }
                //     }
                // }
            }



            DB::commit();

            // if ($validatedData['jenis_retur'] === 'Rusak') {
            //     echo "Ini Rusak";
            // } else {
            //     echo "Ini Tidak Rusak";
            // }

            return redirect()->route('retur.index')->with('success', 'Berhasil melakukan retur Pemasok');
        } catch (Exception $e) {
            DB::rollBack();
            $errorMessage = 'Error processing return: ' . $e->getMessage();
            $errorLine = 'Line: ' . $e->getLine();
            $errorFile = 'File: ' . $e->getFile();
            $errorFunction = 'Function: ' . __FUNCTION__;

            // Log the error details
            Log::error($errorMessage . ' | ' . $errorFile . ' | ' . $errorLine . ' | ' . $errorFunction);

            return redirect()->back()->withErrors([
                'message' => $errorMessage,
                'line' => $errorLine,
                'file' => $errorFile,
                'function' => $errorFunction
            ]);
        }
    }



    public function update(Request $request, $id_retur)
    {


        $request->validate([
            'no_retur_pemasok' => 'required',
            'faktur_retur_pemasok' => 'required',
            'tanggal_retur' => 'required',
            'bukti_retur_pemasok' => 'required',
            'jenis_retur' => 'required|in:Rusak,Tidak Rusak',
            'total_nilai_retur' => 'required|numeric',
            'pengembalian_data' => 'nullable|string',
            'kekurangan' => 'nullable|string',
            'status' => 'required|in:Belum Selesai,Selesai',
            'id_pemasok' => 'required|exists:pemasok_barangs,id_pemasok',
        ]);


        $dataRetur = ReturPemasokModel::find($id_retur);

        if ($dataRetur) {
            $dataRetur->no_retur_pemasok = $request->no_retur_pemasok;
            $dataRetur->faktur_retur_pemasok = $request->faktur_retur_pemasok;
            $dataRetur->tanggal_retur = $request->tanggal_retur;
            $fileData = json_decode($request->bukti_retur_pemasok, true);


            // Mendapatkan base64 data dari JSON
            $base64Data = $fileData['data'];
            // Decode base64 data ke file binary
            $fileBinary = base64_decode($base64Data);
            // Direktori tujuan penyimpanan file
            $targetDirectory = public_path('retur/pembeli/');
            if (!file_exists($targetDirectory)) {
                mkdir($targetDirectory, 0777, true); // Buat direktori secara rekursif
            }
            // Tambahkan .gitignore untuk menghindari terkirimnya gambar
            $gitignoreFile = $targetDirectory . '.gitignore';
            if (!file_exists($gitignoreFile)) {
                file_put_contents($gitignoreFile, "*\n!.gitignore"); // Isi .gitignore dengan aturan umum
            }
            // Membuat nama file yang unik (misalnya, gabungan dari id dan nama file)
            $fileName = $fileData['id'] . '_' . date('Ymdhis') . '_' . $fileData['name'];
            // Menyimpan file ke direktori tujuan
            file_put_contents($targetDirectory . $fileName, $fileBinary);
            $dataRetur->bukti_retur_pemasok = $fileName;
            $dataRetur->jenis_retur = $request->jenis_retur;
            $dataRetur->total_nilai_retur = 0;
            $dataRetur->pengembalian_data = 0;
            $dataRetur->kekurangan = 0;
            $dataRetur->status = "Selesai";
            $dataRetur->id_pemasok = $request->id_pemasok;

            $dataRetur->save();

            return redirect()->route('retur.index')->with('success', 'Retur berhasil diperbarui');
        } else {
            return redirect()->route('retur.index')->with('error', 'Retur tidak ditemukan');
        }
    }

    public function destroy($id_retur)
    {

        $dataRetur = ReturPemasokModel::find($this->hashToId($id_retur)->id_retur_pemasok);
        if ($dataRetur) {
            $barang = Barang::with('stokBarang')->find($dataRetur->id_barang);
            // $stokBarangpertama = $barang->stokBarang[0];
            // $stokBarang = StokBarangModel::find($barang->stokBarang[0]->id);
            // $stokBarang->stok_masuk = $stokBarang->stok_masuk + $dataRetur->qty;
            // $stokBarang->save();




            $barang->stok = $barang->stok + $dataRetur->qty;
            $barang->save();



            // Buat instance dari model
            $stokbarangHistory = new StokBarangHistoryModel();
            $stokbarangHistory->id_barang = $barang->id_barang;
            // $stokbarangHistory->stok_masuk = $validatedData['stok_tambah'];
            // $stokbarangHistory->stok_keluar = $item['qty'];
            $stokbarangHistory->stok_masuk = $dataRetur->qty;
            $stokbarangHistory->stok_terkini = $barang->stok;
            $stokbarangHistory->save();
            // Simpan ke log
            $logStokBarang = new LogStokBarangModel();
            $logStokBarang->json_content = [
                'type' => 'retur_pemasok_destroy',
                'data' => []
            ]; // Sesuaikan dengan isi json_content Anda // Sesuaikan dengan isi json_content Anda
            $logStokBarang->tipe_log = 'retur_pemasok_delete';
            $logStokBarang->keterangan = 'Tambah Retur Pemasok ';
            $logStokBarang->id_admin = Auth::user()->id_admin; // Sesuaikan dengan id_admin yang ada
            // $logStokBarang->id_stok_barang = $stokBarang->id; // Sesuaikan dengan id_stok_barang yang ada
            $logStokBarang->id_barang = $barang->id_barang; // Sesuaikan dengan id_barang yang ada
            $logStokBarang->id_stok_barang_history =  $stokbarangHistory->id_stok;
            $logStokBarang->save();
            $dataRetur->delete();
            return redirect()->route('retur.index')->with('success', 'Retur berhasil dihapus');
        } else {
            return redirect()->route('retur.index')->with('error', 'Retur tidak ditemukan');
        }
    }

    // ReturPemasokController.php
    public function hide($id_retur)
    {
        $retur = ReturPemasokModel::where('hash_id_retur_pemasok', $id_retur)->firstOrFail();
        $retur->hidden = 'yes';
        $retur->save();

        return redirect()->route('retur.index')->with('success', 'Retur berhasil disembunyikan');

    }
}
