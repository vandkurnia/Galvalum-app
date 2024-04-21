<?php

namespace App\Http\Controllers;

use App\Models\DiskonModel;
use Illuminate\Http\Request;

class DiskonController extends Controller
{
    public function index()
    {
        $dataDiskon = DiskonModel::all();

        return view('master.diskon.index', compact('dataDiskon'));
    }

    public function edit(Request $request, $id)
    {
        $dataDiskon = DiskonModel::where('hash_id_diskon', $id)->first();
        if (!$dataDiskon) {
            return response()->json([
                'code' => 404,
                'message' => 'Not found',
                'data' => null
            ], 404);
        }


        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => view('master.diskon.edit', compact('dataDiskon'))->render()
        ], 200);
    }
    public function store(Request $request)
    {
      
        $request->validate([
            'kode_diskon' => 'required|string|max:10',
            'nama_diskon' => 'required|string|max:255',
            'type' => 'required|in:percentage,amount',
            'besaran' => 'required|numeric',
            'status' => 'required|in:AKTIF,NONAKTIF',
        ]);
    
        if ($request->type === 'percentage') {
            $request->validate([
                'besaran' => 'required|numeric|between:0,100',
            ]);
        }
    



        DiskonModel::create($request->all());

        return redirect()->route('diskon.index')->with('success', 'Diskon berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_diskon' => 'required|string|max:10',
            'nama_diskon' => 'required|string|max:255',
            'type' => 'required|in:percentage,amount',
            'besaran' => 'required|numeric',
            'status' => 'required|in:AKTIF,NONAKTIF',
        ]);
    
        if ($request->type === 'percentage') {
            $request->validate([
                'besaran' => 'required|numeric|between:0,100',
            ]);
        }
    
        // dd("heheha");

        $diskon = DiskonModel::findOrFail($id);
        $diskon->update($request->all());

        return redirect()->route('diskon.index')->with('success', 'Diskon berhasil diupdate');
    }

    public function destroy($id)
    {
        // $user = User::findOrFail($id);
        // $user->delete();
        $dataDiskon = DiskonModel::where('hash_id_diskon', $id)->first();
        if ($dataDiskon) {
            $dataDiskon->delete();

            return redirect()->route('diskon.index')->with('success', 'Diskon dihapus');
        } else {
            return redirect()->route('diskon.index')->with('error', 'Diskon gagal dihapus');
        }
    }
}
