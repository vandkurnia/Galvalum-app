<?php

namespace App\Http\Controllers;

use App\Models\Pembeli;
use Illuminate\Http\Request;

class PembeliController extends Controller
{
    public function index()
    {
        $dataPembeli = Pembeli::all();
        return view('master.pembeli.index', compact('dataPembeli'));
    }

    public function edit(Request $request, $id)
    {
        $dataPembeli = Pembeli::where('hash_id_pembeli', $id)->first();
        if (!$dataPembeli) {
            return response()->json([
                'code' => 404,
                'message' => 'Not found',
                'data' => null
            ], 404);
        }


        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => view('master.pembeli.edit', compact('dataPembeli'))->render()
        ], 200);
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama_pembeli' => 'required',
            'alamat_pembeli' => 'required',
            'no_hp_pembeli' => 'required'

        ]);



        // Array data user dari request
        $dataPembeli = [
            'nama_pembeli' => $request->nama_pembeli,
            'alamat_pembeli' => $request->alamat_pembeli,
            'no_hp_pembeli' => $request->no_hp_pembeli

        ];
        Pembeli::create($dataPembeli);

        return redirect()->route('pembeli.index')->with('success', 'Pembeli herhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pembeli' => 'required',
            'alamat_pembeli' => 'required',
            'no_hp_pembeli' => 'required'

        ]);
        // dd("heheha");

        $dataPembeli = Pembeli::where('hash_id_pembeli', $id)->first();

        $dataPembeli->update($request->all());

        return redirect()->route('pembeli.index')->with('success', 'Tipe barang berhasil diupdate');
    }

    public function destroy($id)
    {
        // $user = User::findOrFail($id);
        // $user->delete();
        $dataPembeli = Pembeli::where('hash_id_pembeli', $id)->first();
        if ($dataPembeli) {
            $dataPembeli->delete();

            return redirect()->route('pembeli.index')->with('success', 'Tipe barang dihapus');
        } else {
            return redirect()->route('pembeli.index')->with('error', 'Tipe barang gagal dihapus');
        }
    }
}
