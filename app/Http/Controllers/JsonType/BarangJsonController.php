<?php

namespace App\Http\Controllers\JsonType;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangJsonController extends Controller
{

    public function getSemuaBarangData(Request $request)
    {

        $keyword = $request->get('query');
        $dataSemuaBarang =  Barang::where('nama_barang', 'like', '%' . $keyword . '%')->with('TipeBarang')->paginate(2)->toArray();

        if (!empty($dataSemuaBarang)) {

            $dataBarangTambahan = [];
            foreach ($dataSemuaBarang['data'] as $barang) {
                $barang['id'] = $barang['hash_id_barang'];
                $barang['text'] = $barang['nama_barang'];
                $dataBarangTambahan[] = $barang;
            }

            $dataSemuaBarang['data'] = $dataBarangTambahan;
            $SemuaBarangKeCollection = collect($dataSemuaBarang);
            return response()->json([
                'code' => 404,
                'message' => "Data Berhasil ditemukan",
                'items' => $SemuaBarangKeCollection
            ]);
        } else {
            return response()->json([
                'code' => 404,
                'message' => "Data tidak ditemukan",
            ]);
        }
        // $dataSemuaBarang = Barang::all()->map(function ($barang) {
        //     return [
        //         'id' => $barang->hash_id_barang,
        //         'text' => $barang->nama_barang

        //     ];
        // });



    }
}
