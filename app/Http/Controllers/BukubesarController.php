<?php

namespace App\Http\Controllers;

use App\Models\AkunBayarModel;
use App\Models\BukubesarModel;
use Illuminate\Http\Request;

class BukubesarController extends Controller
{
    public function index()
    {
        $dataBukuBesar = BukubesarModel::all();
        $dataAkunBayar = AkunBayarModel::all();

        return view('bukubesar.index', compact('dataBukuBesar', 'dataAkunBayar'));
    }

    public function edit(Request $request, $id)
    {
        $dataBukuBesar = BukubesarModel::where('hash_id_bukubesar', $id)->first();
        if (!$dataBukuBesar) {
            return response()->json([
                'code' => 404,
                'message' => 'Not found',
                'data' => null
            ], 404);
        }


        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => view('bukubesar.edit', compact('dataBukuBesar'))->render()
        ], 200);
    }
    public function store(Request $request)
    {

        $data = $request->validate([
            'id_akunbayar' => 'nullable|exists:akun_bayar,hash_id_akunbayar',
            'tanggal' => 'required|date',
            'kategori' => 'required|max:255',
            'keterangan' => 'required|max:255',
            'tipe_bukubesar' => 'required|in:debit,kredit',
            'nominal' => 'required|numeric',
        ]);




        $akunBayar = AkunBayarModel::where('hash_id_akunbayar', $data['id_akunbayar'])->first();

        if ($akunBayar) {
            if ($data['tipe_bukubesar'] === 'debit') {
                $akunBayar->saldo += $data['nominal'];
            } else {
                // Tambahkan kredit ke saldo jika tipe bukubesar bukan debit
                $akunBayar->saldo -= $data['nominal'];
            }

            // Simpan perubahan saldo di AkunBayar
            $akunBayar->save();

            $dataBukuBesar = [
                'id_akunbayar' => $akunBayar->id_akunbayar,
                'tanggal' => $data['tanggal'],
                'kategori' => $data['kategori'],
                'keterangan' => $data['keterangan'],
                'debit' => $data['tipe_bukubesar'] === 'debit' ? $data['nominal'] : 0,
                'kredit' => $data['tipe_bukubesar'] === 'kredit' ? $data['nominal'] : 0,
            ];

            BukubesarModel::create($dataBukuBesar);

            return redirect()->route('bukubesar.index')->with('success', 'Data berhasil disimpan');
        } else {
            return redirect()->back()->with('error', 'AkunBayar tidak ditemukan');
        }

        return redirect()->route('bukubesar.index')->with('success', 'Bukubesar berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'kategori' => 'required|max:255',
            'keterangan' => 'required|max:255',
        ]);

        $bukubesar = BukubesarModel::findOrFail($id);
        $bukubesar->update([
            'tanggal' => $data['tanggal'],
            'kategori' => $data['kategori'],
            'keterangan' => $data['keterangan'],
        ]);

        return redirect()->route('bukubesar.index')->with('success', 'Bukubesar berhasil diupdate');
    }

    public function destroy($id)
    {
        // $user = User::findOrFail($id);
        // $user->delete();
        $dataBukuBesar = BukubesarModel::where('hash_id_bukubesar', $id)->first();


        if ($dataBukuBesar) {

            // Ambil akun bayar berdasarkan id_akunbayar di bukubesar
            $akunBayar = AkunBayarModel::where('id_akunbayar', $dataBukuBesar->id_akunbayar)->first();

            if ($akunBayar) {
                // Kurangi saldo jika tipe dataB$dataBukuBesar adalah kredit
                if ($dataBukuBesar->kredit > 0) {
                    $akunBayar->saldo += $dataBukuBesar->kredit;
                }
                // Tambah saldo jika tipe dataB$dataBukuBesar adalah debit
                if ($dataBukuBesar->debit > 0) {
                    $akunBayar->saldo -= $dataBukuBesar->debit;
                }

                // Simpan perubahan saldo di AkunBayar
                $akunBayar->save();
            }
            $dataBukuBesar->delete();

            return redirect()->route('bukubesar.index')->with('success', 'Bukubesar dihapus');
        } else {
            return redirect()->route('bukubesar.index')->with('error', 'Bukubesar gagal dihapus');
        }
    }
}
