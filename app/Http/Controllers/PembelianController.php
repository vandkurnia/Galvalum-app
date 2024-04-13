<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\NotaPembeli;
use App\Models\Pembeli;
use App\Models\PesananPembeli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembelianController extends Controller
{
    public function edit(Request $request, $id)
    {
        $dataPembeli = PesananPembeli::where('hash_id_pesanan', $id)->first();
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
            'jenis_pembelian' => 'required|string',
            'status_pembelian' => 'required|string',
            'id_pembeli' => 'required|string',

            'pesanan' => 'required|array'

        ]);

        DB::beginTransaction();

        $pesananData = json_decode($request->get('pesanan')[0], true);
        // Get Data pembeli
        $pembeliData = Pembeli::firstOrCreate(
            ['hash_id_pembeli' => $request->get('id_pembeli')],
            [
                'nama_pembeli' => $request->id_pembeli,
                'alamat_pembeli' => $request->alamat_pembeli,
                'no_hp_pembeli' => $request->no_hp,
            ] // Isi dengan data default jika pembeli baru dibuat
        );

        $notaPembeli = new NotaPembeli;
        $notaPembeli->jenis_pembelian = $request->get('jenis_pembelian'); // Contoh nilai untuk jenis_pembelian
        $notaPembeli->status_pembelian = $request->get('status_pembelian'); // Contoh nilai untuk status_pembelian
        $notaPembeli->id_pembeli = $pembeliData->id_pembeli; // Contoh nilai untuk id_pembeli
        $notaPembeli->id_admin = Auth::id();; // Contoh nilai untuk id_admin

        $notaPembeli->save();


        // Perulangan untuk pesanan
        foreach ($pesananData as $pesanan) {
            // Data barang 
            $barangData = Barang::where('hash_id_barang', $pesanan['id_barang'])->lockForUpdate()->first();
            // Buat data baru untuk PesananPembeli yang terhubung dengan NotaPembeli dan Barang
            $pesananPembeli = new PesananPembeli;
            $pesananPembeli->jumlah_pembelian = $pesanan['jumlah_pesanan']; // Contoh nilai untuk jumlah_pembelian

            $pesananPembeli->id_nota = $notaPembeli->id_nota; // Gunakan ID NotaPembeli yang baru saja dibuat
            $pesananPembeli->id_barang = $barangData->id_barang; // Gunakan ID Barang yang baru saja dibuat
            $pesananPembeli->save();
            // Array data user dari request


            // Update data barang
            if ($barangData->stok >=  $pesanan['jumlah_pesanan']) {
                $barangData->stok = $barangData->stok - $pesanan['jumlah_pesanan'];
                $barangData->save();
            } else {
                DB::rollBack();
                return redirect()->back()->with('error', 'Gagal memasukkan data');
            }
        }


        DB::commit();


        return redirect()->route('beranda')->with('success', 'Pesanan herhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {


        $request->validate([
            'jenis_pembelian' => 'required|string',
            'status_pembelian' => 'required|string',
            'id_pembeli' => 'required|string',

            'pesanan' => 'required|array'

        ]);

        $notaPembeli = NotaPembeli::where('id_nota', $id)->first();
        $pesananData = $request->get('pesanan');
        // Get Data pembeli
        $pembeliData = Pembeli::where('hash_id_pembeli', $request->get('id_pembeli'))->first();

        $notaPembeli = new NotaPembeli;
        $notaPembeli->jenis_pembelian = $request->get('jenis_pembelian'); // Contoh nilai untuk jenis_pembelian
        $notaPembeli->status_pembelian = $request->get('status_pembelian'); // Contoh nilai untuk status_pembelian
        $notaPembeli->id_pembeli = $pembeliData->id_pembeli; // Contoh nilai untuk id_pembeli
        $notaPembeli->id_admin = Auth::id(); // Contoh nilai untuk id_admin
        $notaPembeli->save();
        // Perulangan untuk pesanan
        // foreach ($pesananData as $pesanan) {
        //     // Data barang 
        //     $barangData = Barang::where('hash_id_barang', $request->get('id'))->first();
        //     // Buat data baru untuk PesananPembeli yang terhubung dengan NotaPembeli dan Barang
        //     $pesananPembeli = new PesananPembeli;
        //     $pesananPembeli->jumlah_pembelian = $pesanan['jumlah_pembelian']; // Contoh nilai untuk jumlah_pembelian
        //     $pesananPembeli->id_nota = $notaPembeli->id_nota; // Gunakan ID NotaPembeli yang baru saja dibuat
        //     $pesananPembeli->id_barang = $barangData->id_barang; // Gunakan ID Barang yang baru saja dibuat
        //     $pesananPembeli->save();
        //     // Array data user dari request
        // }
        return redirect()->route('beranda')->with('success', 'Pesanan herhasil ditambahkan');
    }

    public function destroy($id)
    {
        // $user = User::findOrFail($id);
        // $user->delete();
        $notaPembeli = notaPembeli::where('id_nota', $id)->first();
        if ($notaPembeli) {
            $notaPembeli->delete();

            return redirect()->route('beranda')->with('success', 'Nota Pembelian dihapus');
        } else {
            return redirect()->route('beranda')->with('error', 'Nota Pembelian gagal dihapus');
        }
    }
}
