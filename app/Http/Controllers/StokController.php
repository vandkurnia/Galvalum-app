<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BukubesarModel;
use App\Models\PemasokBarang;
use App\Models\StokBarangModel;
use App\Models\TipeBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StokController extends Controller
{
    public function index()
    {
        $dataSemuaBarang = Barang::with('pemasok', 'tipeBarang', 'stokBarang')->get();
        $dataBaruSemuaBarang = [];
        foreach ($dataSemuaBarang as $barang) {
            $totalStok = $barang->stokBarang->sum('stok_masuk') - $barang->stokBarang->sum('stok_keluar');
            $barang->stok = $totalStok;
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
        $barang->save();



        // Buat record baru untuk BukuBesar
        $bukuBesar = new BukubesarModel();

        $bukuBesar->id_akunbayar = 1; // Isi dengan nilai id_akunbayar yang sesuai
        $bukuBesar->tanggal = date('Y-m-d'); // Isi dengan tanggal yang sesuai
        $bukuBesar->kategori = "barang"; // Isi dengan kategori yang sesuai
        $bukuBesar->keterangan = 'STOK BARANG ' . $barang->id_barang . ' STOK- ' . $request->stok; // Isi dengan keterangan yang sesuai
        $bukuBesar->debit = $request->stok * $request->harga_barang_pemasok; // Isi dengan nilai kredit yang sesuai
        $bukuBesar->save();

        StokBarangModel::create([
            'stok_masuk' => $request->stok,
            'id_barang' => $barang->id_barang,
            'id_bukubesar' => $bukuBesar->id_bukubesar
        ]);
        DB::commit();


        return redirect()->route('stok.index')->with('success', 'Data  Barang Berhasil disimpan');
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'id_pemasok' => 'nullable|exists:pemasok_barangs,id_pemasok',
            'kode_barang' => 'required',
            'nama_barang' => 'required|string|max:255',
            'ukuran' => 'required|string|max:255',
            'id_tipe_barang' => 'required|exists:tipe_barangs,id_tipe_barang',
            'harga_barang' => 'required|numeric|min:0',
            'harga_barang_pemasok' => 'required|numeric|min:0',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Mencari barang berdasarkan hash_id_barang
        $barang = Barang::where('hash_id_barang', $id)->first();

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

        // Menghitung total stok
        $stokBarang = StokBarangModel::where('id_barang', $barang->id_barang)
            ->selectRaw('SUM(stok_masuk - stok_keluar) as stok')
            ->first();

        // Update total
        $barang->total = $stokBarang->stok * $barang->harga_barang_pemasok;

        $barang->save();

        return redirect()->route('stok.index')->with('success', 'Data barang berhasil diperbarui');
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
        $totalStok = $dataBarang->stokBarang->sum('stok_masuk') - $dataBarang->stokBarang->sum('stok_keluar');
        $dataBarang->stok = $totalStok;
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


        // $bukuBesar = new BukubesarModel();
        // $bukuBesar->tanggal = date('Y-m-d');
        // $bukuBesar->kategori =  "barang";
        // $bukuBesar->sub_kategori = "hutang";
        // $bukuBesar->kredit = $validatedData['stok_tambah'] * $barang->harga_barang_pemasok;
        // $bukuBesar->keterangan = "penambahan stok " . $validatedData['stok_tambah'];
        // $bukuBesar->save();

        StokBarangModel::create([
            'stok_masuk' => $validatedData['stok_tambah'],
            'id_barang' => $barang->id_barang
        ]);


        // Mencari barang berdasarkan hash_id_barang
        $barang = Barang::find($barang->id_barang);

        // Kembalikan jika barang tidak ada
        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ada');
        }
        // Menghitung total stok
        $stokBarang = StokBarangModel::where('id_barang', $barang->id_barang)
            ->selectRaw('SUM(stok_masuk - stok_keluar) as stok')
            ->first();

        // Update total
        $barang->total = $stokBarang->stok * $barang->harga_barang_pemasok;

   
        $barang->save();

        DB::commit();

        return redirect()->route('stok.index')->with('success', 'Berhasil mengupdate stok barang.');
    }
    public function minusStok(Request $request)
    {


        $validatedData = $request->validate([
            'stok_kurang' => 'required|numeric|min:0',
            'id_barang' => 'required|exists:barangs,hash_id_barang',
        ], [
            'stok_kurang.required' => 'Pengurangan Stok harus diisi.',
            'stok_kurang.numeric' => 'Pengurangan Stok harus berupa angka.',
            'stok_kurang.min' => 'Pengurangan Stok harus lebih dari atau sama dengan 0.',
            'id_barang.exists' => 'Barang tidak ditemukan.',
        ]);

        $barang = Barang::where('hash_id_barang', $validatedData['id_barang'])->first();

        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan.');
        }

        if ($validatedData['stok_kurang'] <= 0) {
            return redirect()->back()->with('error', 'Pengurangan tidak valid atau 0.');
        }


        DB::beginTransaction();
        // $bukuBesar = new BukubesarModel();
        // $bukuBesar->kategori = "barang"; // Isi dengan kategori yang sesuai
        // $bukuBesar->keterangan = 'STOK BARANG ' . $barang->hash_id_barang . ' STOK- ' . $request->stok; // Isi dengan keterangan yang sesuai
        // $bukuBesar->tanggal = date('Y-m-d');
        // $bukuBesar->sub_kategori = "hutang";
        // $bukuBesar->debit = $validatedData['stok_kurang'];
        // $bukuBesar->keterangan = "Pengurangan stok " . $validatedData['stok_kurang'];
        // $bukuBesar->save();


        StokBarangModel::create([
            'stok_keluar' => $validatedData['stok_kurang'],
            'id_barang' => $barang->id_barang
            // 'id_bukubesar' => $bukuBesar->id_bukubesar
        ]);



        // Mencari barang berdasarkan hash_id_barang
        $barang = Barang::find($barang->id_barang);

        // Kembalikan jika barang tidak ada
        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ada');
        }
        // Menghitung total stok
        $stokBarang = StokBarangModel::where('id_barang', $barang->id_barang)
            ->selectRaw('SUM(stok_masuk - stok_keluar) as stok')
            ->first();

        // Update total
        $barang->total = $stokBarang->stok * $barang->harga_barang_pemasok;

        $barang->save();
        DB::commit();

        return redirect()->route('stok.index')->with('success', 'Berhasil mengupdate stok barang.');
    }
}
