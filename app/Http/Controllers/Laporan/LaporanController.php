<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\KasKeluar;
use App\Models\ModalTambahanModel;
use App\Models\BukubesarModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Carbon;
use League\Csv\Writer;
use PDF;
use App\Models\NotaPembeli;
use Illuminate\Support\Facades\DB;
class LaporanController extends Controller
{

    public function generateCSV()
    {
        $modal_tambahan = ModalTambahanModel::all();

        foreach ($modal_tambahan as $modal) {
            $modal->tanggal = Carbon::parse($modal->tanggal);
        }

        $csv = view('csv.modal_tambahan', compact('modal_tambahan'))->render();

        // Set header untuk file CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Laporan Modal Tambahan.csv"',
        ];

        // Mengembalikan response CSV ke browser
        return Response::stream(function() use ($csv) {
            echo $csv;
        }, 200, $headers);
    }

    public function kaskeluarCSV()
    {
        $kas_keluar = KasKeluar::all();

        foreach ($kas_keluar as $kk) {
            $kk->tanggal = Carbon::parse($kk->tanggal);
        }

        $csv = view('csv.kas_keluar', compact('kas_keluar'))->render();

        // Set header untuk file CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Laporan Kas Keluar.csv"',
        ];

        // Mengembalikan response CSV ke browser
        return Response::stream(function() use ($csv) {
            echo $csv;
        }, 200, $headers);
    }

    public function generatePDF()
    {
        $laporan_modal_tambahan = ModalTambahanModel::all();

        $pdf = PDF::loadView('pdf.invoice', compact('laporan_modal_tambahan'));

        return $pdf->download('Laporan Modal Tambahan.pdf');
    }

    public function kaskeluarPDF(Request $request)
    {
        // Ambil tanggal dari inputan form, jika tidak ada, gunakan null
        $tanggal = $request->input('tanggal');

        // Query data KasKeluar sesuai dengan tanggal yang diinputkan
        $laporan_kas_keluar = KasKeluar::query();

        // Jika tanggal diinputkan, tambahkan filter berdasarkan tanggal
        if ($tanggal) {
            $laporan_kas_keluar->whereDate('tanggal', $tanggal);
        }

        // Ambil data sesuai dengan filter yang sudah ditetapkan
        $laporan_kas_keluar = $laporan_kas_keluar->get();

        // Load view PDF dengan data yang sudah difilter
        $pdf = PDF::loadView('pdf.kas', compact('laporan_kas_keluar'));

        // Download PDF
        return $pdf->download('Laporan Kas Keluar.pdf');
    }

    public function laporanOmzet()
    {

        $request = new Request;
        $tanggalDariInput = $request->get('tanggal') ?? date('Y-m-d');

        $tanggalSaatIni = date('Y-m-d', strtotime($tanggalDariInput));
        $dataNotaPembelian = DB::select(
            '
            SELECT 
                DATE(nota_pembelis.created_at) AS created_at,
                COUNT(pesanan_pembelis.id_pesanan) AS total_pesanan,
                SUM(nota_pembelis.total) AS omzet
            FROM 
                nota_pembelis
            JOIN 
                pesanan_pembelis ON pesanan_pembelis.id_nota = nota_pembelis.id_nota
            WHERE 
                DATE(nota_pembelis.created_at) = ?
            GROUP BY 
                created_at
            ',
            [$tanggalSaatIni]
        );

        $dataNotaPembelian = json_decode(json_encode($dataNotaPembelian), true);

        // Logika untuk mengambil data dan menampilkan laporan omzet
        return view('laporan.laporanomzet', compact('dataNotaPembelian'));
    }

    public function laporanHutang()
    {
        // Logika untuk mengambil data dan menampilkan laporan hutang
        return view('laporan.laporanhutang');
    }

    public function laporanPiutang()
    {


        $request = new Request;
        $tanggalDariInput = $request->get('tanggal') ?? date('Y-m-d');

        $tanggalSaatIni = date('Y-m-d', strtotime($tanggalDariInput));
        $dataNotaPembelian = DB::select(
            '
            SELECT 
            nota_pembelis.id_nota,
            pembelis.nama_pembeli,
            pembelis.no_hp_pembeli,
            COUNT(pesanan_pembelis.id_pesanan) AS total_pembelian,
            DATE(nota_pembelis.created_at) AS tanggal_pembelian,
            nota_pembelis.total,
            nota_pembelis.nominal_terbayar as terbayar,
            nota_pembelis.tenggat_bayar as jatuh_tempo,
            CASE
                WHEN nota_pembelis.total > nota_pembelis.nominal_terbayar THEN "Belum Lunas"
                WHEN nota_pembelis.total < nota_pembelis.nominal_terbayar THEN "Kelebihan"
                ELSE "Lunas"
            END AS status_bayar
        FROM 
            nota_pembelis
        JOIN 
            pesanan_pembelis ON pesanan_pembelis.id_nota = nota_pembelis.id_nota
        JOIN 
            pembelis ON pembelis.id_pembeli = nota_pembelis.id_pembeli
        JOIN
            nota_bukubesar ON nota_bukubesar.id_nota = nota_pembelis.id_nota
        JOIN
            bukubesar ON bukubesar.id_bukubesar = nota_bukubesar.id_bukubesar
        WHERE 
            bukubesar.kategori = "transaksi" AND bukubesar.sub_kategori = "PIUTANG" 
        GROUP BY
            nota_pembelis.id_nota, pembelis.nama_pembeli, pembelis.no_hp_pembeli, nota_pembelis.total, nota_pembelis.tenggat_bayar, nota_pembelis.created_at, nota_pembelis.nominal_terbayar
        
  
            ',
            []
        );


        $dataNotaPembelian = json_decode(json_encode($dataNotaPembelian), true);
        
        // Logika untuk mengambil data dan menampilkan laporan piutang
        return view('laporan.laporanpiutang', compact('dataNotaPembelian'));
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
        $tanggal = $request->input('tanggal');
        
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

    public function labaRugi(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
       /*  $tanggal = $request->input('tanggal', '2024-04-25'); */
        $kategori = $request->input('kategori', 'transaksi'); // Kategori default adalah 'transaksi'
        $kategori_modal = $request->input('kategori', 'modal awal'); // Kategori default adalah 'transaksi'

        $penjualan_kotor = BukubesarModel::where('tanggal', $tanggal)
                    ->where('kategori', $kategori)
                    ->get();

        $modal = BukubesarModel::where('tanggal', $tanggal)
                    ->where('kategori', $kategori_modal)->where('debit', '>', 0)
                    ->get();

        $modal_darurat = BukubesarModel::where('tanggal', $tanggal)
                    ->where('kategori', $kategori_modal)->where('kredit', '>', 0)
                    ->get();
        $total_modal_darurat = $modal_darurat->sum('kredit');

        $pengeluaran = KasKeluar::where('tanggal', $tanggal)->get();
        $total_pengeluaran = $pengeluaran->sum('jumlah_pengeluaran');

        $tambahan_modal = ModalTambahanModel::where('tanggal', $tanggal)->get();
        $jumlah_tambahan_modal = $tambahan_modal->sum('jumlah_modal');

        $total_penjualan_kotor = $penjualan_kotor->sum('debit');
        $total_modal = $modal->sum('debit');

        $total1 = $total_penjualan_kotor + $total_modal;
        $laba_kotor = $total1 + $jumlah_tambahan_modal;
        $laba_bersih = $laba_kotor - $total_pengeluaran;
        $total_transfer = $laba_bersih - $total_modal_darurat;

        // Logika untuk mengambil data dan menampilkan laporan laba rugi
        return view('laporan.laba_rugi', compact('penjualan_kotor', 'tambahan_modal', 'jumlah_tambahan_modal', 'pengeluaran', 'total_pengeluaran', 'modal', 'modal_darurat', 'total1', 'laba_kotor', 'laba_bersih', 'total_transfer'));
    }

    public function labaRugiPDF(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        // $tanggal = $request->input('tanggal', '2024-04-25');
        $kategori = $request->input('kategori', 'transaksi'); // Kategori default adalah 'transaksi'
        $kategori_modal = $request->input('kategori', 'modal awal'); // Kategori default adalah 'transaksi'

        $penjualan_kotor = BukubesarModel::where('tanggal', $tanggal)
                    ->where('kategori', $kategori)
                    ->get();

        $modal = BukubesarModel::where('tanggal', $tanggal)
                    ->where('kategori', $kategori_modal)->where('debit', '>', 0)
                    ->get();

        $modal_darurat = BukubesarModel::where('tanggal', $tanggal)
                    ->where('kategori', $kategori_modal)->where('kredit', '>', 0)
                    ->get();
        $total_modal_darurat = $modal_darurat->sum('kredit');

        $pengeluaran = KasKeluar::where('tanggal', $tanggal)->get();
        $total_pengeluaran = $pengeluaran->sum('jumlah_pengeluaran');

        $tambahan_modal = ModalTambahanModel::where('tanggal', $tanggal)->get();
        $jumlah_tambahan_modal = $tambahan_modal->sum('jumlah_modal');

        $total_penjualan_kotor = $penjualan_kotor->sum('debit');
        $total_modal = $modal->sum('debit');

        $total1 = $total_penjualan_kotor + $total_modal;
        $laba_kotor = $total1 + $jumlah_tambahan_modal;
        $laba_bersih = $laba_kotor - $total_pengeluaran;
        $total_transfer = $laba_bersih - $total_modal_darurat;

        $namaHari = array(
            'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'
        );
    
        // Mendapatkan indeks hari dari tanggal yang diberikan
        $indeksHari = date('w', strtotime($tanggal));
    
        // Mendapatkan nama hari dalam bahasa Indonesia
        $hari = $namaHari[$indeksHari];
        $tanggal = strftime("%d %B %Y", strtotime($tanggal));

        // Logika untuk mengambil data dan menampilkan laporan laba rugi
        $pdf = PDF::loadView('pdf.laba_rugi', compact('penjualan_kotor', 'tambahan_modal', 'jumlah_tambahan_modal', 'pengeluaran', 'total_pengeluaran', 'modal', 'modal_darurat', 'total1', 'laba_kotor', 'laba_bersih', 'total_transfer', 'hari', 'tanggal'));

        return $pdf->download('Laporan Laba Rugi.pdf');
    }

    public function LabaRugiCSV(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        // $tanggal = $request->input('tanggal', '2024-04-25');
        $kategori = $request->input('kategori', 'transaksi'); // Kategori default adalah 'transaksi'
        $kategori_modal = $request->input('kategori', 'modal awal'); // Kategori default adalah 'transaksi'

        $penjualan_kotor = BukubesarModel::where('tanggal', $tanggal)
                    ->where('kategori', $kategori)
                    ->get();

        $modal = BukubesarModel::where('tanggal', $tanggal)
                    ->where('kategori', $kategori_modal)->where('debit', '>', 0)
                    ->get();

        $modal_darurat = BukubesarModel::where('tanggal', $tanggal)
                    ->where('kategori', $kategori_modal)->where('kredit', '>', 0)
                    ->get();
        $total_modal_darurat = $modal_darurat->sum('kredit');

        $pengeluaran = KasKeluar::where('tanggal', $tanggal)->get();
        $total_pengeluaran = $pengeluaran->sum('jumlah_pengeluaran');

        $tambahan_modal = ModalTambahanModel::where('tanggal', $tanggal)->get();
        $jumlah_tambahan_modal = $tambahan_modal->sum('jumlah_modal');

        $total_penjualan_kotor = $penjualan_kotor->sum('debit');
        $total_modal = $modal->sum('debit');

        $total1 = $total_penjualan_kotor + $total_modal;
        $laba_kotor = $total1 + $jumlah_tambahan_modal;
        $laba_bersih = $laba_kotor - $total_pengeluaran;
        $total_transfer = $laba_bersih - $total_modal_darurat;

        $namaHari = array(
            'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'
        );
    
        // Mendapatkan indeks hari dari tanggal yang diberikan
        $indeksHari = date('w', strtotime($tanggal));
    
        // Mendapatkan nama hari dalam bahasa Indonesia
        $hari = $namaHari[$indeksHari];
        $tanggal = strftime("%d %B %Y", strtotime($tanggal));

        // Buat string CSV dari data
        $csv = view('csv.laba_rugi', compact('penjualan_kotor', 'tambahan_modal', 'jumlah_tambahan_modal', 'pengeluaran', 'total_pengeluaran', 'modal', 'modal_darurat', 'total1', 'laba_kotor', 'laba_bersih', 'total_transfer', 'hari', 'tanggal'))->render();

        // Set header untuk file CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Laporan Laba Rugi.csv"',
        ];

        // Mengembalikan response CSV ke browser
        return Response::stream(function() use ($csv) {
            echo $csv;
        }, 200, $headers);
    }
}
