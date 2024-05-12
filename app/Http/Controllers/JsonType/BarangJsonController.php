<?php

namespace App\Http\Controllers\JsonType;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\StokBarangModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangJsonController extends Controller
{

    public function getSemuaBarangData(Request $request)
    {

        $keyword = $request->get('query');
        $dataSemuaBarang =  Barang::where('nama_barang', 'like', '%' . $keyword . '%')->with('TipeBarang', 'stokBarang')->paginate(2)->toArray();

        if (!empty($dataSemuaBarang)) {

            $dataBarangTambahan = [];
            foreach ($dataSemuaBarang['data'] as $barang) {
                $barang['id'] = $barang['hash_id_barang'];
                $barang['text'] = $barang['nama_barang'];

                $stokBarang = StokBarangModel::where('id_barang', $barang['id_barang'])
                    ->selectRaw('(SUM(stok_masuk) - SUM(stok_keluar)) as stok')
                    ->groupBy('id_barang')
                    ->first();

                $barang['stok'] = $stokBarang->stok;
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
