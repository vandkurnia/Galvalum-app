<?php

namespace App\Http\Controllers\Retur;

use App\Http\Controllers\Controller;
use App\Models\Pembeli;
use App\Models\Retur\ReturPembeliModel;
use Illuminate\Http\Request;

class ReturPembeliController extends Controller
{

    private function  hashToId($hash_id)
    {

        return ReturPembeliModel::where('hash_id_retur_pembeli', $hash_id)->first();
    }
    public function edit($id_retur)
    {

        $dataReturPembeli = ReturPembeliModel::find($this->hashToId($id_retur)->id_retur_pembeli);
        $dataPembeli = Pembeli::all();
        return view('retur.pembeli.edit', compact('dataReturPembeli', 'dataPembeli'));
    }
    public function add($id_pesanan)
    {

        $dataPembeli = Pembeli::all();
        return view('retur.pembeli.add', compact('id_pesanan', 'dataPembeli'));
    }
    public function store(Request $request)
    {

        $request->validate([
            'no_retur_pembeli' => 'required',
            'faktur_retur_pembeli' => 'required',
            'tanggal_retur_pembeli' => 'required',
            'bukti_retur_pembeli' => 'required',
            'jenis_retur' => 'required|in:Rusak,Tidak Rusak',
            'total_nilai_retur' => 'required|numeric',
            'pengembalian_data' => 'nullable|string',
            'kekurangan' => 'nullable|string',
            'status' => 'required|in:Belum Selesai,Selesai',
            'id_pembeli' => 'required|exists:pembelis,id_pembeli',
        ]);

        $dataReturPembeli = new ReturPembeliModel();
        $dataReturPembeli->no_retur_pembeli = $request->no_retur_pembeli;
        $dataReturPembeli->faktur_retur_pembeli = $request->faktur_retur_pembeli;
        $dataReturPembeli->tanggal_retur_pembeli = $request->tanggal_retur_pembeli;

        $file = $request->file('bukti_retur_pembeli');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('public/retur_pembeli/', $fileName);
        $dataReturPembeli->bukti_retur_pembeli = $fileName;

        $dataReturPembeli->jenis_retur = $request->jenis_retur;
        $dataReturPembeli->total_nilai_retur = $request->total_nilai_retur;
        $dataReturPembeli->pengembalian_data = $request->pengembalian_data;
        $dataReturPembeli->kekurangan = $request->kekurangan;
        $dataReturPembeli->status = $request->status;
        $dataReturPembeli->id_pembeli = $request->id_pembeli;

        $dataReturPembeli->save();



        return redirect()->route('retur.index')->with('success', 'Retur berhasil ditambahkan');
    }

    public function update(Request $request, $id_retur)
    {


        $request->validate([

            'no_retur_pembeli' => 'required',
            'faktur_retur_pembeli' => 'required',
            'tanggal_retur_pembeli' => 'required',
            'jenis_retur' => 'required|in:Rusak,Tidak Rusak',
            'total_nilai_retur' => 'required|numeric',
            'pengembalian_data' => 'nullable|string',
            'kekurangan' => 'nullable|string',
            'status' => 'required|in:Belum Selesai,Selesai',
            'id_pembeli' => 'required|exists:pembelis,id_pembeli',
        ]);
        $returPembeli = new ReturPembeliModel;
        $returPembeli->update([

            'no_retur_pembeli' => $request->no_retur_pembeli,
            'faktur_retur_pembeli' => $request->faktur_retur_pembeli,
            'tanggal_retur_pembeli' => $request->tanggal_retur_pembeli,
            'jenis_retur' => $request->jenis_retur,
            'total_nilai_retur' => $request->total_nilai_retur,
            'pengembalian_data' => $request->pengembalian_data,
            'kekurangan' => $request->kekurangan,
            'status' => $request->status,
            'id_pembeli' => $request->id_pembeli,
        ]);

        if ($request->hasFile('bukti_retur_pembeli')) {
            $file = $request->file('bukti_retur_pembeli');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/retur_pembeli/', $fileName);
            $returPembeli->bukti_retur_pembeli = $fileName;
            $returPembeli->save();
        }



        return redirect()->route('retur.index')->with('success', 'Retur berhasil diperbarui');
    }

    public function destroy($id_retur)
    {
        $dataReturPembeli = ReturPembeliModel::find($this->hashToId($id_retur)->id_retur_pembeli);
        $dataReturPembeli->delete();

        return redirect()->route('retur.index')->with('success', 'Retur berhasil dihapus');
    }
}
