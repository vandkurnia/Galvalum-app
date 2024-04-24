<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\KasKeluar;
use App\Models\ModalTambahanModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use League\Csv\Writer;
use PDF;

class LaporanController extends Controller
{

    public function generateCSV()
    {
        $users = ModalTambahanModel::all();
        $numbers = 1;

        $csv = Writer::createFromString('');

        // Add CSV header
        $csv->insertOne(['No', 'tanggal', 'Jenis Modal Tambahan', 'Deskripsi', 'Jumlah Modal']);

        // Add data
        foreach ($users as $user) {
            $csv->insertOne([$numbers++, $user->tanggal, $user->jenis_modal_tambahan, $user->deskripsi, $user->jumlah_modal]);
        }

        // Set HTTP headers
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Modal Tambahan.csv"',
        ];

        // Return CSV file as response
        return Response::make($csv->getContent(), 200, $headers);
    }

    public function kaskeluarCSV()
    {
        $users = KasKeluar::all();
        $numbers = 1;

        $csv = Writer::createFromString('');

        // Add CSV header
        $csv->insertOne(['No', 'tanggal', 'Nama Pengeluaran', 'Deskripsi', 'Jumlah Pengeluaran']);

        // Add data
        foreach ($users as $user) {
            $csv->insertOne([$numbers++, $user->tanggal, $user->nama_pengeluaran, $user->deskripsi, $user->jumlah_pengeluaran]);
        }

        // Set HTTP headers
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Laporan kas Keluar.csv"',
        ];

        // Return CSV file as response
        return Response::make($csv->getContent(), 200, $headers);
    }

    public function generatePDF()
    {
        $laporan_modal_tambahan = ModalTambahanModel::all();

        $pdf = PDF::loadView('pdf.invoice', compact('laporan_modal_tambahan'));

        return $pdf->download('Laporan Modal Tambahan.pdf');
    }

    public function kaskeluarPDF()
    {
        $laporan_kas_keluar = KasKeluar::all();

        $pdf = PDF::loadView('pdf.kas', compact('laporan_kas_keluar'));

        return $pdf->download('Laporan Kas Keluar.pdf');
    }

    public function laporanOmzet()
    {
        // Logika untuk mengambil data dan menampilkan laporan omzet
        return view('laporan.laporanomzet');
    }

    public function laporanHutang()
    {
        // Logika untuk mengambil data dan menampilkan laporan hutang
        return view('laporan.laporanhutang');
    }

    public function laporanPiutang()
    {
        // Logika untuk mengambil data dan menampilkan laporan piutang
        return view('laporan.laporanpiutang');
    }

    public function kasKeluar()
    {
        $laporan_kas_keluar = KasKeluar::all();
        // Logika untuk mengambil data dan menampilkan laporan kas keluar
        return view('laporan.kaskeluar', compact('laporan_kas_keluar'));
    }

    public function simpanKas(Request $request)
    {
        $request->validate([
            'nama_pengeluaran' => 'required',
            'deskripsi' => 'required',
            'jumlah_pengeluaran' => 'required',
            'tanggal' => 'required',

        ]);

        // Array data user dari request
        $Keluar = [
            'nama_pengeluaran' => $request->nama_pengeluaran,
            'deskripsi' => $request->deskripsi,
            'jumlah_pengeluaran' => $request->jumlah_pengeluaran,
            'tanggal' => $request->tanggal,

        ];
        KasKeluar::create($Keluar);

        return redirect()->route('laporan.kaskeluar')->with('success', 'Kas Keluar herhasil ditambahkan');
    }

    public function editKas(Request $request, $id)
    {
        $laporan_kas_keluar = KasKeluar::where('id_kas_keluar', $id)->first();
        if (!$laporan_kas_keluar) {
            return response()->json([
                'code' => 404,
                'message' => 'Not found',
                'data' => null
            ], 404);
        }


        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => view('laporan.kaskeluar_edit', compact('laporan_kas_keluar'))->render()
        ], 200);
    }

    public function updateKas(Request $request, $id)
    {
        $request->validate([
            'nama_pengeluaran' => 'required',
            'deskripsi' => 'required',
            'jumlah_pengeluaran' => 'required',
            'tanggal' => 'required',

        ]);
        // dd("heheha");

        $laporan_kas_keluar = kaskeluar::where('id_kas_keluar', $id)->first();
      
        $laporan_kas_keluar->update($request->all());

        return redirect()->route('laporan.kaskeluar')->with('success', 'Tipe barang berhasil diupdate');
    }

    public function destroy($id)
    {
        // $user = User::findOrFail($id);
        // $user->delete();
        $dataTipeBarang = KasKeluar::where('id_kas_keluar', $id)->first();
        if ($dataTipeBarang) {
            $dataTipeBarang->delete();

            return redirect()->route('laporan.kaskeluar')->with('success', 'Tipe barang dihapus');
        } else {
            return redirect()->route('laporan.kaskeluar')->with('error', 'Tipe barang gagal dihapus');
        }
    }

    public function filterKas(Request $request)
    {
        $tanggal = $request->tanggal;
        
        $laporan_kas_keluar = KasKeluar::whereDate('tanggal','=',$tanggal)->get();
        // Logika untuk mengambil data dan menampilkan laporan kas keluar
        return view('laporan.kaskeluar',compact('laporan_kas_keluar'));
    }

    public function modalTambahan()
    {
        $laporan_modal_tambahan = ModalTambahanModel::all();
        // Logika untuk mengambil data dan menampilkan laporan modal tambahan
        return view('laporan.modal_tambahan', compact('laporan_modal_tambahan'));
    }

    public function editModal(Request $request, $id)
    {
        $laporan_modal_tambahan = ModalTambahanModel::where('id_modal_tambahan', $id)->first();
        if (!$laporan_modal_tambahan) {
            return response()->json([
                'code' => 404,
                'message' => 'Not found',
                'data' => null
            ], 404);
        }


        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => view('laporan.modaledit', compact('laporan_modal_tambahan'))->render()
        ], 200);
    }

    public function updateModal(Request $request, $id)
    {
        $request->validate([
            'jenis_modal_tambahan' => 'required',
            'deskripsi' => 'required',
            'jumlah_modal' => 'required',
            'tanggal' => 'required',

        ]);
        // dd("heheha");

        $laporan_modal_tambahan = ModalTambahanModel::where('id_modal_tambahan', $id)->first();
      
        $laporan_modal_tambahan->update($request->all());

        return redirect()->route('laporan.modaltambahan')->with('success', 'Modal Tambahan berhasil diupdate');
    }

    public function modaldestroy($id)
    {
        // $user = User::findOrFail($id);
        // $user->delete();
        $laporan_modal_tambahan = ModalTambahanModel::where('id_modal_tambahan', $id)->first();
        if ($laporan_modal_tambahan) {
            $laporan_modal_tambahan->delete();

            return redirect()->route('laporan.modaltambahan')->with('success', 'Modal tambahan dihapus');
        } else {
            return redirect()->route('laporan.modaltambahan')->with('error', 'Modal tambahan gagal dihapus');
        }
    }

    public function filterModal(Request $request)
    {
        $tanggal = $request->tanggal;
        
        $laporan_modal_tambahan = ModalTambahanModel::whereDate('tanggal','=',$tanggal)->get();
        // Logika untuk mengambil data dan menampilkan laporan kas keluar
        return view('laporan.modal_tambahan',compact('laporan_modal_tambahan'));
    }

    public function simpanModal(Request $request)
    {
        $request->validate([
            'jenis_modal_tambahan' => 'required',
            'deskripsi' => 'required',
            'jumlah_modal' => 'required',
            'tanggal' => 'required',

        ]);

        // Array data user dari request
        $Keluar = [
            'jenis_modal_tambahan' => $request->jenis_modal_tambahan,
            'deskripsi' => $request->deskripsi,
            'jumlah_modal' => $request->jumlah_modal,
            'tanggal' => $request->tanggal,

        ];
        ModalTambahanModel::create($Keluar);

        return redirect()->route('laporan.modaltambahan')->with('success', 'Modal Tambahan herhasil ditambahkan');
    }

    public function labaRugi()
    {
        // Logika untuk mengambil data dan menampilkan laporan laba rugi
        return view('laporan.laba_rugi');
    }
}
