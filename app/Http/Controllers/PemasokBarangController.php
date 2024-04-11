<?php

namespace App\Http\Controllers;

use App\Models\PemasokBarang;
use Illuminate\Http\Request;

class PemasokBarangController extends Controller
{

    public function showAllPemasok()
    {
        $dataPemasokTerpilih = PemasokBarang::all();
        return $dataPemasokTerpilih;
    }

    public function index()
    {
        $dataPemasokTerpilih = PemasokBarang::all();
        return view('master.pemasokbarang.index', compact('dataPemasokTerpilih'));
        // return view('master.tipebarang.index');
    }

    public function edit(Request $request, $id)
    {
        $dataPemasokTerpilih = PemasokBarang::where('hash_id_barang', $id)->first();
        if (!$dataPemasokTerpilih) {
            return response()->json([
                'code' => 404,
                'message' => 'Not found',
                'data' => null
            ], 404);
        }


        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => view('master.pemasokbarang.edit', compact('dataPemasokTerpilih'))->render()
        ], 200);
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama_pemasok' => 'required',
            'no_telp_pemasok' => 'required',
            'alamat_pemasok' => 'required',
        ]);




        // Array data user dari request
        $user = [
            'nama_pemasok' => $request->nama_pemasok,
            'no_telp_pemasok' => $request->no_telp_pemasok,
            'alamat_pemasok' => $request->alamat_pemasok,
        ];
        PemasokBarang::create($user);

        return redirect()->route('pemasokbarang.index')->with('success', 'Data Pemasok Barang Berhasil disimpan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pemasok' => 'required',
            'no_telp_pemasok' => 'required',
            'alamat_pemasok' => 'required',
            // 'email_admin' => 'required|email|unique:users,email_admin',
        ]);
        // dd("heheha");

        $dataPemasokTerpilih = PemasokBarang::where('hash_id_pemasok', $id)->first();

        $dataPemasokTerpilih->update($request->all());

        return redirect()->route('pemasokbarang.index')->with('success', 'Data Pemasok Barang berhasil diupdate');
    }

    public function destroy($id)
    {
        $dataPemasokTerpilih = PemasokBarang::where('hash_id_pemasok', $id)->first();
        if ($dataPemasokTerpilih) {
            $dataPemasokTerpilih->delete();

            return redirect()->route('pemasokbarang.index')->with('success', 'Pemasok barang berhasil dihapus');
        } else {
            return redirect()->route('pemasokbarang.index')->with('error', 'Pemasok barang gagal dihapus');
        }
    }
}
