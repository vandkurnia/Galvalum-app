<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BukubesarModel;
use App\Models\PemasokBarang;
use App\Models\TipeBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StokController extends Controller
{
    public function index()
    {

        $dataSemuaBarang = Barang::with('pemasok', 'tipeBarang')->get();
        $dataTipeBarang = TipeBarang::all();
        $dataPemasok = PemasokBarang::all();
        return view('stokbarang.index', ['dataSemuaBarang' => $dataSemuaBarang, 'dataPemasok' => $dataPemasok, 'dataTipeBarang' => $dataTipeBarang]);

        // return view('stokbarang.index');
    }
    public function edit(Request $request, $id)
    {
        $dataBarang = Barang::where('hash_id_barang', $id)->first();
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
            'id_pemasok' => 'required',
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
        $barang->harga_barang_pemasok =  $request->harga_barang_pemasok;
        $barang->stok = $request->stok;
        $barang->ukuran = $request->ukuran;
        $barang->id_pemasok = $request->id_pemasok;
        $barang->id_tipe_barang = $request->id_tipe_barang;
        $barang->save();

        // Buat record baru untuk BukuBesar
        $bukuBesar = new BukubesarModel();

        $bukuBesar->id_akunbayar = 1; // Isi dengan nilai id_akunbayar yang sesuai
        $bukuBesar->tanggal = date('Y-m-d'); // Isi dengan tanggal yang sesuai
        $bukuBesar->kategori = "barang"; // Isi dengan kategori yang sesuai
        $bukuBesar->keterangan = 'HUTANG STOK BARANG ' . $barang->id_barang . ' STOK- ' . $request->stok; // Isi dengan keterangan yang sesuai
        $bukuBesar->debit = 0; // Isi dengan nilai debit yang sesuai
        $bukuBesar->kredit = $request->stok * $request->harga_barang_pemasok; // Isi dengan nilai kredit yang sesuai
        $bukuBesar->sub_kategori = 'hutang'; // Isi dengan sub kategori yang sesuai
        $bukuBesar->save();
        // Hubungkan Barang dengan BukuBesar
        $barang->bukuBesar()->attach($bukuBesar->id_bukubesar);
        DB::commit();



        return redirect()->route('stok.index')->with('success', 'Data  Barang Berhasil disimpan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_barang' => 'required|unique:barangs,kode_barang',
            'nama_barang' => 'required',
            'harga_barang' => 'required',
            'harga_barang_pemasok' => 'required',
            'stok' => 'required',
            'ukuran' => 'required',
            'id_pemasok' => 'required',
            'id_tipe_barang' => 'required',
        ]);

        // dd("heheha");

        $dataBarang = Barang::where('hash_id_barang', $id)->first();

        $dataBarang->update([
            'kode_barang' => $request->get('kode_barang'),
            'nama_barang' => $request->get('nama_barang'),
            'harga_barang' => $request->get('harga_barang'),
            // 'harga_barang_pemasok' => $request->get('harga_barang_pemasok'),
            'ukuran' =>  $request->get('ukuran'),
            'id_pemasok' => $request->get('id_pemasok'),
            'id_tipe_barang' => $request->get('id_tipe_barang')

        ]);

        return redirect()->route('stok.index')->with('success', 'Data Barang berhasil diupdate');
    }

    public function destroy($id)
    {
        $dataBarang = Barang::where('hash_id_barang', $id)->first();
        if ($dataBarang) {
            $dataBarang->delete();

            return redirect()->route('stok.index')->with('success', 'Barang berhasil dihapus');
        } else {
            return redirect()->route('stok.index')->with('error', 'Barang gagal dihapus');
        }
    }
}
