<?php

namespace App\Http\Controllers;

use App\Models\TipeBarang;
use Illuminate\Http\Request;

class TipeBarangController extends Controller
{
    public function index()
    {
        $dataTipeBarang = TipeBarang::all();
        return view('master.tipebarang.index', compact('dataTipeBarang'));
        // return view('master.tipebarang.index');
    }

    public function edit(Request $request, $id)
    {
        $dataTipeBarang = TipeBarang::where('hash_id_tipe_barang', $id)->first();
        if (!$dataTipeBarang) {
            return response()->json([
                'code' => 404,
                'message' => 'Not found',
                'data' => null
            ], 404);
        }


        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => view('master.tipebarang.edit', compact('dataTipeBarang'))->render()
        ], 200);
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama_tipe' => 'required',

        ]);



        // Array data user dari request
        $dataTipeBarang = [
            'nama_tipe' => $request->nama_tipe,

        ];
        TipeBarang::create($dataTipeBarang);

        return redirect()->route('tipebarang.index')->with('success', 'Tipe barang herhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_tipe' => 'required',

        ]);
        // dd("heheha");

        $dataTipeBarang = TipeBarang::where('hash_id_tipe_barang', $id)->first();
      
        $dataTipeBarang->update($request->all());

        return redirect()->route('tipebarang.index')->with('success', 'Tipe barang berhasil diupdate');
    }

    public function destroy($id)
    {
        // $user = User::findOrFail($id);
        // $user->delete();
        $dataTipeBarang = TipeBarang::where('hash_id_tipe_barang', $id)->first();
        if ($dataTipeBarang) {
            $dataTipeBarang->delete();

            return redirect()->route('tipebarang.index')->with('success', 'Tipe barang dihapus');
        } else {
            return redirect()->route('tipebarang.index')->with('error', 'Tipe barang gagal dihapus');
        }
    }
}
