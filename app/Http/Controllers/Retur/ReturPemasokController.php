<?php

namespace App\Http\Controllers\Retur;

use App\Http\Controllers\Controller;
use App\Models\PemasokBarang;
use App\Models\PesananPembeli;
use App\Models\Retur\ReturPemasokModel;
use Illuminate\Http\Request;

class ReturPemasokController extends Controller
{

    private function  hashToId($hash_id)
    {

        return ReturPemasokModel::where('hash_id_retur_pemasok', $hash_id)->first();
    }
    public function edit($id_retur)
    {
        $dataReturPemasok = ReturPemasokModel::find($this->hashToId($id_retur)->id_retur_pemasok);
        $dataPemasok = PemasokBarang::all();
        return view('retur.pemasok.edit', compact('dataReturPemasok', 'dataPemasok'));
    }
    public function add($id_pesanan)
    {

        $dataPemasok = PemasokBarang::all();
        return view('retur.pemasok.add', compact('id_pesanan', 'dataPemasok'));
    }
    public function store(Request $request)
    {

        $request->validate([
            'no_retur_pemasok' => 'required',
            'faktur_retur_pemasok' => 'required',
            'tanggal_retur' => 'required',
            'bukti_retur_pemasok' => 'required',
            'jenis_retur' => 'required|in:Rusak,Tidak Rusak',
            'total_nilai_retur' => 'required|numeric',
            'pengembalian_data' => 'nullable|string',
            'kekurangan' => 'nullable|string',
            'status' => 'required|in:Belum Selesai,Selesai',
            'id_pemasok' => 'required|exists:pemasok_barangs,id_pemasok',
        ]);

        $dataRetur = new ReturPemasokModel();
        $dataRetur->no_retur_pemasok = $request->no_retur_pemasok;
        $dataRetur->faktur_retur_pemasok = $request->faktur_retur_pemasok;
        $dataRetur->tanggal_retur = $request->tanggal_retur;
        $file = $request->file('bukti_retur_pemasok');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('public/retur/', $fileName);

        $dataRetur->bukti_retur_pemasok = $fileName;

        $dataRetur->jenis_retur = $request->jenis_retur;
        $dataRetur->total_nilai_retur = $request->total_nilai_retur;
        $dataRetur->pengembalian_data = $request->pengembalian_data;
        $dataRetur->kekurangan = $request->kekurangan;
        $dataRetur->status = $request->status;
        $dataRetur->id_pemasok = $request->id_pemasok;

        $dataRetur->save();



        // Tanya penjelasan perihal ini
        // if( $dataRetur->jenis_retur == "Rusak")
        // {
        //     $dataBarang = Barang::find($dataPesanan->id_barang);
        //     $dataBarang->stok = $dataBarang->stok 

        //     $dataPesanan->jumlah_pembelian
        // }

        return redirect()->route('retur.index')->with('success', 'Retur berhasil ditambahkan');
    }

    public function update(Request $request, $id_retur)
    {


        $request->validate([
            'no_retur_pemasok' => 'required',
            'faktur_retur_pemasok' => 'required',
            'tanggal_retur' => 'required',
            'bukti_retur_pemasok' => 'required',
            'jenis_retur' => 'required|in:Rusak,Tidak Rusak',
            'total_nilai_retur' => 'required|numeric',
            'pengembalian_data' => 'nullable|string',
            'kekurangan' => 'nullable|string',
            'status' => 'required|in:Belum Selesai,Selesai',
            'id_pemasok' => 'required|exists:pemasok_barangs,id_pemasok',
        ]);

        $dataRetur = ReturPemasokModel::find($id_retur);

        if ($dataRetur) {
            $dataRetur->no_retur_pemasok = $request->no_retur_pemasok;
            $dataRetur->faktur_retur_pemasok = $request->faktur_retur_pemasok;
            $dataRetur->tanggal_retur = $request->tanggal_retur;
            if ($request->hasFile('bukti_retur_pemasok')) {
                $file = $request->file('bukti_retur_pemasok');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/retur/', $fileName);
                $dataRetur->bukti_retur_pemasok = $fileName;
            }
            $dataRetur->jenis_retur = $request->jenis_retur;
            $dataRetur->total_nilai_retur = $request->total_nilai_retur;
            $dataRetur->pengembalian_data = $request->pengembalian_data;
            $dataRetur->kekurangan = $request->kekurangan;
            $dataRetur->status = $request->status;
            $dataRetur->id_pemasok = $request->id_pemasok;

            $dataRetur->save();

            return redirect()->route('retur.index')->with('success', 'Retur berhasil diperbarui');
        } else {
            return redirect()->route('retur.index')->with('error', 'Retur tidak ditemukan');
        }
    }

    public function destroy($id_retur)
    {

        $dataRetur = ReturPemasokModel::find($this->hashToId($id_retur)->id_retur_pemasok);
        if ($dataRetur) {
            $dataRetur->delete();
            return redirect()->route('retur.index')->with('success', 'Retur berhasil dihapus');
        } else {
            return redirect()->route('retur.index')->with('error', 'Retur tidak ditemukan');
        }
    }
}
