<?php

namespace App\Http\Controllers\Retur;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\PemasokBarang;
use App\Models\PesananPembeli;
use App\Models\Retur\ReturPemasokModel;
use App\Models\StokBarangModel;
use Exception;
use Illuminate\Http\Request;
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
        $totalStok = $dataBarang->stokBarang->sum(function ($stok) {
            return $stok->stok_masuk - $stok->stok_keluar;
        });
        return view('retur.pemasok.add', compact('id_barang', 'dataBarang', 'totalStok'));
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tanggal_retur_pemasok' => 'required|date',
            'bukti_retur_pemasok' => 'required', // 10MB max
            'jenis_retur' => 'required|in:Rusak,Tidak Rusak',
            'retur_data' => 'required'
        ]);

        $returData = json_decode($validatedData['retur_data'], true);

        DB::beginTransaction();
        try {
            foreach ($returData as $item) {
                // Validate each item
                $barang = Barang::with('Pemasok')->find($item['id_barang']);
                if (!$barang) {
                    throw new Exception('Barang Tidak ada');
                }

                // Calculate stock
                $stok = StokBarangModel::where('id_barang', $item['id_barang'])
                    ->selectRaw('SUM(stok_masuk - stok_keluar) as stok')
                    ->value('stok');

                if ($stok < $item['qty']) {
                    throw new Exception('Stok tidak cukup untuk retur');
                }


                // Generate No Retur
                $totalIdReturPembeli = ReturPemasokModel::count();
                if ($totalIdReturPembeli === 0) {
                    $totalIdReturPembeli = 1;
                } else {
                    $totalIdReturPembeli = $totalIdReturPembeli + 1;
                }

                $NoreturPembeli = "RETUR" . date('YmdHis') . $totalIdReturPembeli;




                // Olah File Uploadan Bukti

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
                $buktiReturPemasok = $fileName;

                // Create StokBarang record
                $stokBarang = StokBarangModel::create([
                    'id_barang' => $barang->id_barang,
                    'stok_keluar' => $item['qty'],
                ]);

                // Create ReturPemasok record
                ReturPemasokModel::create([
                    'no_retur_pemasok' => $NoreturPembeli,
                    'tanggal_retur' => $validatedData['tanggal_retur_pemasok'],
                    'bukti_retur_pemasok' => $buktiReturPemasok,
                    'jenis_retur' => $validatedData['jenis_retur'],
                    'total_nilai_retur' => $item['total'],
                    'pengembalian_data' => '0',
                    'kekurangan' => '0',
                    'status' => 'Selesai',
                    'id_pemasok' => $barang->Pemasok->id_pemasok ?? null,
                    'id_barang' =>$barang->id_barang,
                    'id_stok_barang' => $stokBarang->id,
                ]);
            }

            DB::commit();

            if ($validatedData['jenis_retur'] === 'Rusak') {
                echo "Ini Rusak";
            } else {
                echo "Ini Tidak Rusak";
            }

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
            $dataRetur->delete();
            return redirect()->route('retur.index')->with('success', 'Retur berhasil dihapus');
        } else {
            return redirect()->route('retur.index')->with('error', 'Retur tidak ditemukan');
        }
    }
}
