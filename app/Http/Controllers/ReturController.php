<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\PesananPembeli;
use App\Models\Retur;
use Illuminate\Http\Request;

class ReturController extends Controller
{
    public function index()
    {
        $dataRetur = Retur::all();
        return view('retur.retur', compact('dataRetur'));
    }
    public function edit($id_retur)
    {
        $dataRetur = Retur::find($id_retur);
        return view('retur.edit', compact('dataRetur'));
    }
    public function add($id_pesanan)
    {

        return view('retur.add', compact('id_pesanan'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'id_pesanan' => 'required',
            'tanggal_retur' => 'required|date',
            'bukti' => 'required|file',
            'jenis_retur' => 'required|in:Rusak,Tidak Rusak',
            'keterangan' => 'nullable|string',
        ]);

        $retur = new Retur();
        $retur->tanggal_retur = $request->tanggal_retur;
        $retur->jenis_retur = $request->jenis_retur;
        $retur->keterangan = $request->keterangan;

        // Upload file
        $file = $request->file('bukti');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('public/retur/bukti', $fileName);
        $retur->bukti = $fileName;
        $retur->id_pesanan = $request->id_pesanan;

        $retur->save();

        // Hapus barang setelah retur
        $dataPesanan = PesananPembeli::find($request->id_pesanan);
        $dataPesanan->delete();


        // Tanya penjelasan perihal ini
        // if($retur->jenis_retur == "Rusak")
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
            'tanggal_retur' => 'required|date',
            'bukti' => 'nullable|file',
            'jenis_retur' => 'required|in:Rusak,Tidak Rusak',
            'keterangan' => 'nullable|string',
            'id_pesanan' => 'required'
        ]);

        $retur = Retur::find($id_retur);
        $retur->tanggal_retur = $request->tanggal_retur;
        $retur->jenis_retur = $request->jenis_retur;
        $retur->keterangan = $request->keterangan;
        $retur->id_pesanan = $request->id_pesanan;

        // Update file if provided
        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/bukti', $fileName);
            $retur->bukti = $fileName;
        }

        $retur->save();

        return redirect()->route('retur.index')->with('success', 'Retur berhasil diperbarui');
    }

    public function destroy($id_retur)
    {
        $retur = Retur::find($id_retur);
        $retur->delete();

        return redirect()->route('retur.index')->with('success', 'Retur berhasil dihapus');
    }
}
