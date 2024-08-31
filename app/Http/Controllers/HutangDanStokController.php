<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\HutangDanStokModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HutangDanStokController extends Controller
{
    public function index($id_barang)
    {
        $hutangDanStok = HutangDanStokModel::with('bukubesar')->where('id_barang', $id_barang)->get();
        return view('stokbarang.detail', compact('hutangDanStok'));
    }

    public function show($id)
    {
        // $hutangDanStok = HutangDanStokModel::with('bukubesar')->findOrFail($id);
        // return view('hutang-dan-stok.show', compact('hutangDanStok'));
        $hutangDanStok = HutangDanStokModel::findOrFail($id);
    
        return response()->json([
            'total' => $hutangDanStok->total,
            'dp' => $hutangDanStok->dp,
            'nominal_terbayar' => $hutangDanStok->nominal_terbayar,
            'stok' => $hutangDanStok->stok,
            'id_bukubesar' => $hutangDanStok->id_bukubesar,
            'id_barang' => $hutangDanStok->id_barang,
            'updateUrl' => route('hutang-dan-stok.update', ['id' => $hutangDanStok->id_hutang_stok])
        ]);
    }

    public function create()
    {
        return view('hutang-dan-stok.create');
    }

    public function storeRequest(Request $request)
    {
        $validatedData = $request->validate([
            'total' => 'required|numeric',
            'dp' => 'nullable|numeric',
            'nominal_terbayar' => 'nullable|numeric',
            'stok' => 'required|integer',
            'id_bukubesar' => 'required|exists:bukubesar,id',
            'id_barang' => 'required|exists:barang,id_barang',
        ]);

        $result = $this->store($validatedData);

        return redirect()->back()->with($result['status'], $result['message']);
    }

    public function store(array $data)
    {
        try {
            $barang = Barang::find($data['id_barang']);
            if(!$barang)
            {
                return [
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Barang tidak ditemukan',

                ];
            }
            $hutangDanStok = HutangDanStokModel::create([
                'harga_beli' => $barang->harga_barang_pemasok,
                'total' => $data['total'],
                'dp' => $data['dp'],
                'nominal_terbayar' => $data['nominal_terbayar'],
                'stok' => $data['stok'],
                'tenggat_waktu' => $data['tenggat_bayar'],
                'id_bukubesar' => $data['id_bukubesar'],
                'id_barang' => $data['id_barang'],
            ]);

            return [
                'code' => 201,
                'status' => 'success',
                'message' => 'Berhasil menyimpan data',
            ];
        } catch (\Exception $e) {
            return [
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal menyimpan hutang dan stok',
                'detail_error' => json_encode([
                    'line' => $e->getLine(),
                    'errorMsg' => $e->getMessage()
                ])
            ];
        }
    }

    public function edit($id)
    {
        $hutangDanStok = HutangDanStokModel::findOrFail($id);
        return view('hutang-dan-stok.edit', compact('hutangDanStok'));
    }

    public function updateRequest(Request $request, $id)
    {
        $validatedData = $request->validate([
            'total' => 'required|numeric',
            'dp' => 'nullable|numeric',
            'nominal_terbayar' => 'nullable|numeric',
            'stok' => 'required|integer',
            'id_bukubesar' => 'required|exists:bukubesar,id',
            'id_barang' => 'required|exists:barang,id_barang',
        ]);

        $result = $this->update($validatedData, $id);

        return redirect()->back()->with($result['status'], $result['message']);
    }

    public function update(array $data, $id)
    {
        try {
            $hutangDanStok = HutangDanStokModel::findOrFail($id);
        
            $hutangDanStok->update([
                'harga_beli' => $hutangDanStok->harga_beli,
                'total' => $data['total'],
                'dp' => $data['dp'],
                'nominal_terbayar' => $data['nominal_terbayar'],
                'stok' => $data['stok'],

                'tenggat_waktu' => $data['tenggat_bayar'],
                'id_bukubesar' => $data['id_bukubesar'],
                'id_barang' => $data['id_barang'],
                'hidden' => 'no'
            ]);

            return [
                'code' => 200,
                'status' => 'success',
                'message' => 'Berhasil memperbarui data',
            ];
        } catch (\Exception $e) {
            return [
                'code' => 500,
                'status' => 'error',
                'message' => 'Gagal memperbarui data',
            ];
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        $hutangDanStok = HutangDanStokModel::findOrFail($id);

        $checkTotalHutangDanStokTersedia = HutangDanStokModel::where('id_barang', $hutangDanStok->id_barang)->count();
        
        // Minimal harus satu
        if($checkTotalHutangDanStokTersedia <= 1)
        {
            DB::rollBack();
            return redirect()->back()->with('error', 'Data lacak stok gagal dihapus hanya tersisa  1 data');
        }

        $barang =  Barang::find($hutangDanStok->id_barang);
        $barang->stok -= $hutangDanStok->stok;
        $barang->save();


        
        $hutangDanStok->delete();
        DB::commit();

        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }
}
