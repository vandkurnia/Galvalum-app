<?php

namespace App\Http\Controllers\JsonType;

use App\Http\Controllers\Controller;
use App\Models\Pembeli;
use Illuminate\Http\Request;

class PembeliJsonController extends Controller
{
    public function getSemuaPembeliData(Request $request)
    {

        $keyword = $request->get('query');
        $dataSemuaPembeli = Pembeli::where('nama_pembeli', 'like', "%{$keyword}%")->paginate(2)->toArray();


        if (!empty($dataSemuaPembeli)) {

            $dataPembeliTambahan = [];
            foreach ($dataSemuaPembeli['data'] as $Pembeli) {
                $Pembeli['id'] = $Pembeli['hash_id_pembeli'];
                $Pembeli['text'] = $Pembeli['nama_pembeli'];
                $dataPembeliTambahan[] = $Pembeli;
            }

            $dataSemuaPembeli['data'] = $dataPembeliTambahan;
            $semuaPembeliCollection = collect($dataSemuaPembeli);
            return response()->json([
                'code' => 404,
                'message' => "Data Berhasil ditemukan",
                'items' => $semuaPembeliCollection
            ]);
        } else {
            return response()->json([
                'code' => 404,
                'message' => "Data tidak ditemukan",
            ]);
        }
    }
}
