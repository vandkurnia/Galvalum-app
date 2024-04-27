<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AkunBayarModel;
use App\Models\BukubesarModel;
use App\Models\KategoriModel;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $dataBukuBesar = BukubesarModel::all();
        $dataAkunBayar = AkunBayarModel::all();
        $dataKategori = KategoriModel::all();

        return view('kategori.index', compact('dataBukuBesar', 'dataAkunBayar', 'dataKategori'));
    }

    public function edit(Request $request, $id)
    {
        $dataKategori = KategoriModel::where('id_kategori', $id)->first();
        if (!$dataKategori) {
            return response()->json([
                'code' => 404,
                'message' => 'Not found',
                'data' => null
            ], 404);
        }


        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => view('kategori.edit', compact('dataKategori'))->render()
        ], 200);
    }

    public function simpanKategori(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required',
        ]);

        // Array data user dari request
        $Kategori = [
            'nama_kategori' => $request->nama_kategori,
        ];
        KategoriModel::create($Kategori);

        return redirect()->route('kategori.index')->with('success', 'Kategori herhasil ditambahkan');
    }
    
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nama_kategori' => 'required',
        ]);

        $kategori = kategoriModel::findOrFail($id);
        $kategori->update([
            'nama_kategori' => $data['nama_kategori'],
        ]);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diupdate');
    }

    public function destroyKategori($id)
    {
        $kategori = KategoriModel::where('id_kategori', $id)->first();
        if ($kategori) {
            $kategori->delete();

            return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus');
        } else {
            return redirect()->route('kategori.index')->with('error', 'Kategori gagal dihapus');
        }
    }
}
