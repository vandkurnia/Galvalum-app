<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\PemasokBarang;
use App\Models\TipeBarang;
use Illuminate\Http\Request;

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
            'nama_barang' => 'required',
            'harga_barang' => 'required',
            'stok' => 'required',
            'ukuran' => 'required',
            'id_pemasok' => 'required',
            'id_tipe_barang' => 'required',
        ]);




        // Array data user dari request
        $user = [
            'nama_barang' => $request->nama_barang,
            'harga_barang' => $request->harga_barang,
            'stok' => $request->stok,
            'ukuran' => $request->ukuran,
            'id_pemasok' => $request->id_pemasok,
            'id_tipe_barang' => $request->id_tipe_barang
        ];
        Barang::create($user);

        return redirect()->route('stok.index')->with('success', 'Data  Barang Berhasil disimpan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_barang' => 'required',
            'harga_barang' => 'required',
            'stok' => 'required',
            'ukuran' => 'required',
            'id_pemasok' => 'required',
            'id_tipe_barang' => 'required',
        ]);

        // dd("heheha");

        $dataBarang = Barang::where('hash_id_barang', $id)->first();

        $dataBarang->update($request->all());

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
