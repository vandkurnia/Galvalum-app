<?php

namespace App\Http\Controllers\Laporan;

use App\Http\Controllers\Controller;
use App\Models\KasKeluar;
use App\Models\ModalTambahanModel;
use App\Models\BukubesarModel;
use App\Models\AkunBayarModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Carbon;
use League\Csv\Writer;
use PDF;
use App\Models\NotaPembeli;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanController extends Controller
{

    public function generateCSV(Request $request)
    {
        $kategori = $request->input('kategori', 'modal tambahan');
        $dataAkunBayar = AkunBayarModel::all();
        $modal_tambahan = BukubesarModel::where('kategori', $kategori)->get();
                            
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = [
          'font' => ['bold' => true], // Set font nya jadi bold
          'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
          ],
          'borders' => [
            'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
            'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
            'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
            'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
          ]
        ];
        $style_row = [
            'alignment' => [
              'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
              'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
              'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
              'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
              'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];
        $sheet->setCellValue('A1', "LAPORAN MODAL TAMBAHAN"); // Set kolom A1 dengan tulisan "DATA SISWA"
        $sheet->mergeCells('A1:D1'); // Set Merge Cell pada kolom A1 sampai E1
        $sheet->getStyle('A1')->getFont()->setBold(true); // Set bold kolom A1
        $sheet->getStyle('A1')->applyFromArray($style_col);
        // Buat header tabel nya pada baris ke 3
        $sheet->setCellValue('A2', "NO"); // Set kolom A3 dengan tulisan "NO"
        $sheet->setCellValue('B2', "TANGGAL"); // Set kolom B3 dengan tulisan "NIS"
        $sheet->setCellValue('C2', "DESKRIPSI"); // Set kolom C3 dengan tulisan "NAMA"
        $sheet->setCellValue('D2', "JUMLAH PENGELUARAN"); // Set kolom D3 dengan tulisan "JENIS KELAMIN"
        // Apply style header yang telah kita buat tadi ke masing-masing kolom header
        $sheet->getStyle('A2')->applyFromArray($style_col);
        $sheet->getStyle('B2')->applyFromArray($style_col);
        $sheet->getStyle('C2')->applyFromArray($style_col);
        $sheet->getStyle('D2')->applyFromArray($style_col);

        $no = 1; // Untuk penomoran tabel, di awal set dengan 1
        $numrow = 3; // Set baris pertama untuk isi tabel adalah baris ke 4
        foreach($modal_tambahan as $data){ // Lakukan looping pada variabel siswa
          $sheet->setCellValue('A'.$numrow, $no);
          $sheet->setCellValue('B'.$numrow, $data->tanggal);
          $sheet->setCellValue('C'.$numrow, $data->keterangan);
          $sheet->setCellValue('D'.$numrow, "Rp ".number_format($data->debit, 0, ',', '.'));
          
          // Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
          $sheet->getStyle('A'.$numrow)->applyFromArray($style_row);
          $sheet->getStyle('B'.$numrow)->applyFromArray($style_row);
          $sheet->getStyle('C'.$numrow)->applyFromArray($style_row);
          $sheet->getStyle('D'.$numrow)->applyFromArray($style_row);
          
          $no++; // Tambah 1 setiap kali looping
          $numrow++; // Tambah 1 setiap kali looping
        }
        $sheet->getColumnDimension('A')->setWidth(5); // Set width kolom A
        $sheet->getColumnDimension('B')->setWidth(15); // Set width kolom B
        $sheet->getColumnDimension('C')->setWidth(50); // Set width kolom C
        $sheet->getColumnDimension('D')->setWidth(30); // Set width kolom D
        
        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $sheet->getDefaultRowDimension()->setRowHeight(-1);
        // Set orientasi kertas jadi LANDSCAPE
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        // Set judul file excel nya
        $sheet->setTitle("Laporan Modal Tambahan");
        // Proses file excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Laporan Modal Tambahan.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    public function kaskeluarCSV(Request $request)
    {
        $kategori = $request->input('kategori', 'pengeluaran');
        $dataAkunBayar = AkunBayarModel::all();
        $kas_keluar = BukubesarModel::where('kategori', $kategori)->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = [
          'font' => ['bold' => true], // Set font nya jadi bold
          'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
          ],
          'borders' => [
            'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
            'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
            'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
            'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
          ]
        ];
        $style_row = [
            'alignment' => [
              'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
              'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
              'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
              'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
              'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];
        $sheet->setCellValue('A1', "LAPORAN KAS KELUAR"); // Set kolom A1 dengan tulisan "DATA SISWA"
        $sheet->mergeCells('A1:D1'); // Set Merge Cell pada kolom A1 sampai E1
        $sheet->getStyle('A1')->getFont()->setBold(true); // Set bold kolom A1
        $sheet->getStyle('A1')->applyFromArray($style_col);
        // Buat header tabel nya pada baris ke 3
        $sheet->setCellValue('A2', "NO"); // Set kolom A3 dengan tulisan "NO"
        $sheet->setCellValue('B2', "TANGGAL"); // Set kolom B3 dengan tulisan "NIS"
        $sheet->setCellValue('C2', "DESKRIPSI"); // Set kolom C3 dengan tulisan "NAMA"
        $sheet->setCellValue('D2', "JUMLAH PENGELUARAN"); // Set kolom D3 dengan tulisan "JENIS KELAMIN"
        // Apply style header yang telah kita buat tadi ke masing-masing kolom header
        $sheet->getStyle('A2')->applyFromArray($style_col);
        $sheet->getStyle('B2')->applyFromArray($style_col);
        $sheet->getStyle('C2')->applyFromArray($style_col);
        $sheet->getStyle('D2')->applyFromArray($style_col);

        $no = 1; // Untuk penomoran tabel, di awal set dengan 1
        $numrow = 3; // Set baris pertama untuk isi tabel adalah baris ke 4
        foreach($kas_keluar as $data){ // Lakukan looping pada variabel siswa
          $sheet->setCellValue('A'.$numrow, $no);
          $sheet->setCellValue('B'.$numrow, $data->tanggal);
          $sheet->setCellValue('C'.$numrow, $data->keterangan);
          $sheet->setCellValue('D'.$numrow, "Rp ".number_format($data->kredit, 0, ',', '.'));
          
          // Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
          $sheet->getStyle('A'.$numrow)->applyFromArray($style_row);
          $sheet->getStyle('B'.$numrow)->applyFromArray($style_row);
          $sheet->getStyle('C'.$numrow)->applyFromArray($style_row);
          $sheet->getStyle('D'.$numrow)->applyFromArray($style_row);
          
          $no++; // Tambah 1 setiap kali looping
          $numrow++; // Tambah 1 setiap kali looping
        }

        $sheet->getColumnDimension('A')->setWidth(5); // Set width kolom A
        $sheet->getColumnDimension('B')->setWidth(15); // Set width kolom B
        $sheet->getColumnDimension('C')->setWidth(50); // Set width kolom C
        $sheet->getColumnDimension('D')->setWidth(30); // Set width kolom D
        
        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $sheet->getDefaultRowDimension()->setRowHeight(-1);
        // Set orientasi kertas jadi LANDSCAPE
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        // Set judul file excel nya
        $sheet->setTitle("Laporan Kas Keluar");
        // Proses file excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Laporan Kas Keluar.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    public function generatePDF(Request $request)
    {
        $kategori = $request->input('kategori', 'modal tambahan');
        $dataAkunBayar = AkunBayarModel::all();
        $laporan_modal_tambahan = BukubesarModel::where('kategori', $kategori)
                            ->get();

        $pdf = PDF::loadView('pdf.invoice', compact('laporan_modal_tambahan'));

        return $pdf->download('Laporan Modal Tambahan.pdf');
    }

    public function kaskeluarPDF(Request $request)
    {
        $kategori = $request->input('kategori', 'pengeluaran');
        $dataAkunBayar = AkunBayarModel::all();
        $laporan_kas_keluar = BukubesarModel::where('kategori', $kategori)
                            ->get();
        // Ambil tanggal dari inputan form, jika tidak ada, gunakan null

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

    public function kasKeluar(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        $kategori = $request->input('kategori', 'pengeluaran');
        $dataAkunBayar = AkunBayarModel::all();
        $laporan_kas_keluar = BukubesarModel::where('tanggal', $tanggal)
        ->where('kategori', $kategori)
        ->get();

        $tanggal = strftime("%d %B %Y", strtotime($tanggal));
        // Logika untuk mengambil data dan menampilkan laporan kas keluar
        return view('laporan.kaskeluar', compact('laporan_kas_keluar', 'dataAkunBayar', 'tanggal'));
    }

    public function simpanKas(Request $request)
    {
        $request->validate([
            'id_akunbayar' => 'nullable|exists:akun_bayar,hash_id_akunbayar',
            'keterangan' => 'required',
            'kredit' => 'required',
            'tanggal' => 'required',

        ]);

        $akunBayar = AkunBayarModel::where('hash_id_akunbayar', $request['id_akunbayar'])->first();

        // Array data user dari request
        $Keluar = [
            'id_akunbayar' => $akunBayar->id_akunbayar,
            'kategori' => $request->input('kategori', 'pengeluaran'),
            'keterangan' => $request->keterangan,
            'kredit' => $request->kredit,
            'debit' => $request->input('debit', '0'),
            'tanggal' => $request->tanggal,

        ];
        BukubesarModel::create($Keluar);

        return redirect()->route('laporan.kaskeluar')->with('success', 'Kas Keluar herhasil ditambahkan');
    }

    public function editKas(Request $request, $id)
    {
        $laporan_kas_keluar = BukubesarModel::where('id_bukubesar', $id)->first();
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
            'keterangan' => 'required',
            'kredit' => 'required',
            'tanggal' => 'required',

        ]);
        // dd("heheha");

        $laporan_kas_keluar = BukubesarModel::where('id_bukubesar', $id)->first();
      
        $laporan_kas_keluar->update($request->all());

        return redirect()->route('laporan.kaskeluar')->with('success', 'Tipe barang berhasil diupdate');
    }

    public function destroy($id)
    {
        // $user = User::findOrFail($id);
        // $user->delete();
        $dataTipeBarang = BukubesarModel::where('id_bukubesar', $id)->first();
        if ($dataTipeBarang) {
            $dataTipeBarang->delete();

            return redirect()->route('laporan.kaskeluar')->with('success', 'Tipe barang dihapus');
        } else {
            return redirect()->route('laporan.kaskeluar')->with('error', 'Tipe barang gagal dihapus');
        }
    }

    public function filterKas(Request $request)
    {
        $kategori = $request->input('kategori', 'pengeluaran');
        $dataAkunBayar = AkunBayarModel::all();

        $tanggal = $request->input('tanggal');
        

        $laporan_kas_keluar = KasKeluar::where('kategori', $kategori)->whereDate('tanggal','=',$tanggal)->get();
        // Logika untuk mengambil data dan menampilkan laporan kas keluar
        return view('laporan.kaskeluar',compact('laporan_kas_keluar', 'dataAkunBayar'));
    }

    public function modalTambahan(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        $kategori = $request->input('kategori', 'modal tambahan');
        $dataAkunBayar = AkunBayarModel::all();
        $laporan_modal_tambahan = BukubesarModel::where('tanggal', $tanggal)->where('kategori', $kategori)->get();
        // Logika untuk mengambil data dan menampilkan laporan modal tambahan
        return view('laporan.modal_tambahan', compact('laporan_modal_tambahan', 'dataAkunBayar'));
    }

    public function editModal(Request $request, $id)
    {
        $laporan_modal_tambahan = BukubesarModel::where('id_bukubesar', $id)->first();
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
            'keterangan' => 'required',
            'debit' => 'required',
            'tanggal' => 'required',

        ]);
        // dd("heheha");

        $laporan_modal_tambahan = BukubesarModel::where('id_bukubesar', $id)->first();
      
        $laporan_modal_tambahan->update($request->all());

        return redirect()->route('laporan.modaltambahan')->with('success', 'Modal Tambahan berhasil diupdate');
    }

    public function modaldestroy($id)
    {
        // $user = User::findOrFail($id);
        // $user->delete();
        $laporan_modal_tambahan = BukubesarModel::where('id_bukubesar', $id)->first();
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
        
        $laporan_modal_tambahan = BukubesarModel::whereDate('tanggal','=',$tanggal)->get();

        $kategori = $request->input('kategori', 'modal tambahan');
        $dataAkunBayar = AkunBayarModel::all();
        $laporan_modal_tambahan = BukubesarModel::where('kategori', $kategori)->get();

        // Logika untuk mengambil data dan menampilkan laporan kas keluar
        return view('laporan.modal_tambahan',compact('laporan_modal_tambahan', 'kategori', 'dataAkunBayar'));
    }

    public function simpanModal(Request $request)
    {
        $request->validate([
            'id_akunbayar' => 'nullable|exists:akun_bayar,hash_id_akunbayar',
            'keterangan' => 'required',
            'debit' => 'required',
            'tanggal' => 'required',

        ]);

        $akunBayar = AkunBayarModel::where('hash_id_akunbayar', $request['id_akunbayar'])->first();

        // Array data user dari request
        $Keluar = [
            'id_akunbayar' => $akunBayar->id_akunbayar,
            'kategori' => $request->input('kategori', 'modal tambahan'),
            'keterangan' => $request->keterangan,
            'debit' => $request->debit,
            'kredit' => $request->input('kredit', '0'),
            'tanggal' => $request->tanggal,

        ];
        BukubesarModel::create($Keluar);

        return redirect()->route('laporan.modaltambahan')->with('success', 'Modal Tambahan herhasil ditambahkan');
    }

    public function labaRugi(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
       /*  $tanggal = $request->input('tanggal', '2024-04-25'); */
        $kategori = $request->input('kategori', 'transaksi'); // Kategori default adalah 'transaksi'
        $kategori_modal_tambahan = $request->input('kategori', 'modal tambahan'); // Kategori default adalah 'transaksi'
        $kategori_pengeluaran = $request->input('kategori', 'pengeluaran'); // Kategori default adalah 'transaksi'
        $kategori_modal = $request->input('kategori', 'modal awal'); // Kategori default adalah 'transaksi'

        $penjualan_kotor = BukubesarModel::where('tanggal', $tanggal)
                    ->where('kategori', $kategori)
                    ->get();

        $modal_tambahan = BukubesarModel::where('tanggal', $tanggal)
                    ->where('kategori', $kategori_modal_tambahan)
                    ->get();

        $keluar = BukubesarModel::where('tanggal', $tanggal)
                    ->where('kategori', $kategori_pengeluaran)
                    ->get();

        $modal = BukubesarModel::where('tanggal', $tanggal)
                    ->where('kategori', $kategori_modal)->where('debit', '>', 0)
                    ->get();

        $modal_darurat = BukubesarModel::where('tanggal', $tanggal)
                    ->where('kategori', $kategori_modal)->where('kredit', '>', 0)
                    ->get();

        $total_modal_darurat = $modal_darurat->sum('kredit');
        $total_modal_tambahan = $modal_tambahan->sum('debit');
        $total_keluar = $keluar->sum('kredit');

        $pengeluaran = KasKeluar::where('tanggal', $tanggal)->get();
        $total_pengeluaran = $pengeluaran->sum('jumlah_pengeluaran');

        $tambahan_modal = ModalTambahanModel::where('tanggal', $tanggal)->get();
        $jumlah_tambahan_modal = $tambahan_modal->sum('jumlah_modal');

        $total_penjualan_kotor = $penjualan_kotor->sum('debit');
        $total_modal = $modal->sum('debit');

        $total1 = $total_penjualan_kotor + $total_modal;
        $laba_kotor = $total1 + $total_modal_tambahan;
        $laba_bersih = $laba_kotor - $total_keluar;
        $total_transfer = $laba_bersih - $total_modal_darurat;

        // Logika untuk mengambil data dan menampilkan laporan laba rugi
        return view('laporan.laba_rugi', compact('total_modal_darurat', 'total_modal', 'modal_tambahan', 'keluar', 'total_modal_tambahan', 'total_keluar', 'total_penjualan_kotor', 'penjualan_kotor', 'tambahan_modal', 'jumlah_tambahan_modal', 'pengeluaran', 'total_pengeluaran', 'modal', 'modal_darurat', 'total1', 'laba_kotor', 'laba_bersih', 'total_transfer'));
    }

    public function labaRugiPDF(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        // $tanggal = $request->input('tanggal', '2024-04-25');
        $kategori = $request->input('kategori', 'transaksi'); // Kategori default adalah 'transaksi'
        $kategori_modal_tambahan = $request->input('kategori', 'modal tambahan'); // Kategori default adalah 'transaksi'
        $kategori_pengeluaran = $request->input('kategori', 'pengeluaran'); // Kategori default adalah 'transaksi'
        $kategori_modal = $request->input('kategori', 'modal awal'); // Kategori default adalah 'transaksi'

        $penjualan_kotor = BukubesarModel::where('tanggal', $tanggal)
                    ->where('kategori', $kategori)
                    ->get();

        $modal_tambahan = BukubesarModel::where('tanggal', $tanggal)
                    ->where('kategori', $kategori_modal_tambahan)
                    ->get();

        $keluar = BukubesarModel::where('tanggal', $tanggal)
                    ->where('kategori', $kategori_pengeluaran)
                    ->get();

        $modal = BukubesarModel::where('tanggal', $tanggal)
                    ->where('kategori', $kategori_modal)->where('debit', '>', 0)
                    ->get();

        $modal_darurat = BukubesarModel::where('tanggal', $tanggal)
                    ->where('kategori', $kategori_modal)->where('kredit', '>', 0)
                    ->get();

        $total_modal_darurat = $modal_darurat->sum('kredit');
        $total_modal_tambahan = $modal_tambahan->sum('debit');
        $total_keluar = $keluar->sum('kredit');

        $pengeluaran = KasKeluar::where('tanggal', $tanggal)->get();
        $total_pengeluaran = $pengeluaran->sum('jumlah_pengeluaran');

        $tambahan_modal = ModalTambahanModel::where('tanggal', $tanggal)->get();
        $jumlah_tambahan_modal = $tambahan_modal->sum('jumlah_modal');

        $total_penjualan_kotor = $penjualan_kotor->sum('debit');
        $total_modal = $modal->sum('debit');

        $total1 = $total_penjualan_kotor + $total_modal;
        $laba_kotor = $total1 + $total_modal_tambahan;
        $laba_bersih = $laba_kotor - $total_keluar;
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
        $pdf = PDF::loadView('pdf.laba_rugi', compact('total_modal_darurat', 'total_modal', 'modal_tambahan', 'keluar', 'total_modal_tambahan', 'total_keluar', 'total_penjualan_kotor', 'penjualan_kotor', 'tambahan_modal', 'jumlah_tambahan_modal', 'pengeluaran', 'total_pengeluaran', 'modal', 'modal_darurat', 'total1', 'laba_kotor', 'laba_bersih', 'total_transfer', 'hari', 'tanggal'));

        return $pdf->download('Laporan Laba Rugi.pdf');
    }

    public function LabaRugiCSV(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
        // $tanggal = $request->input('tanggal', '2024-04-25');
        $kategori = $request->input('kategori', 'transaksi'); // Kategori default adalah 'transaksi'
        $kategori_modal_tambahan = $request->input('kategori', 'modal tambahan'); // Kategori default adalah 'transaksi'
        $kategori_pengeluaran = $request->input('kategori', 'pengeluaran'); // Kategori default adalah 'transaksi'
        $kategori_modal = $request->input('kategori', 'modal awal'); // Kategori default adalah 'transaksi'

        $penjualan_kotor = BukubesarModel::where('tanggal', $tanggal)
                    ->where('kategori', $kategori)
                    ->get();

        $modal_tambahan = BukubesarModel::where('tanggal', $tanggal)
                    ->where('kategori', $kategori_modal_tambahan)
                    ->get();

        $keluar = BukubesarModel::where('tanggal', $tanggal)
                    ->where('kategori', $kategori_pengeluaran)
                    ->get();

        $modal = BukubesarModel::where('tanggal', $tanggal)
                    ->where('kategori', $kategori_modal)->where('debit', '>', 0)
                    ->get();

        $modal_darurat = BukubesarModel::where('tanggal', $tanggal)
                    ->where('kategori', $kategori_modal)->where('kredit', '>', 0)
                    ->get();

        $total_modal_darurat = $modal_darurat->sum('kredit');
        $total_modal_tambahan = $modal_tambahan->sum('debit');
        $total_keluar = $keluar->sum('kredit');

        $pengeluaran = KasKeluar::where('tanggal', $tanggal)->get();
        $total_pengeluaran = $pengeluaran->sum('jumlah_pengeluaran');

        $tambahan_modal = ModalTambahanModel::where('tanggal', $tanggal)->get();
        $jumlah_tambahan_modal = $tambahan_modal->sum('jumlah_modal');

        $total_penjualan_kotor = $penjualan_kotor->sum('debit');
        $total_modal = $modal->sum('debit');

        $total1 = $total_penjualan_kotor + $total_modal;
        $laba_kotor = $total1 + $total_modal_tambahan;
        $laba_bersih = $laba_kotor - $total_keluar;
        $total_transfer = $laba_bersih - $total_modal_darurat;

        $namaHari = array(
            'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'
        );
    
        // Mendapatkan indeks hari dari tanggal yang diberikan
        $indeksHari = date('w', strtotime($tanggal));
    
        // Mendapatkan nama hari dalam bahasa Indonesia
        $hari = $namaHari[$indeksHari];
        $tanggal = strftime("%d %B %Y", strtotime($tanggal));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = [
          'font' => ['bold' => true], // Set font nya jadi bold
          'borders' => [
            'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
            'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
            'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
            'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
          ]
        ];

        $style_top1 = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
              'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
              'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
              'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
              'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];

        $style_top2 = [
            'alignment' => [
              'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
              'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
              'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
              'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
              'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];
        $sheet->setCellValue('A1', "REKAP RINCIAN PENJUALAN"); // Set kolom A1 dengan tulisan "DATA SISWA"
        $sheet->mergeCells('A1:C1'); // Set Merge Cell pada kolom A1 sampai E1
        $sheet->getStyle('A1')->getFont()->setBold(true); // Set bold kolom A1
        $sheet->getStyle('A1:C1')->applyFromArray($style_top1);
        $sheet->getStyle('A1:C1')->getFill()
          ->setFillType(Fill::FILL_SOLID)
          ->getStartColor()->setRGB('FFB43C');

        $sheet->setCellValue('A2', $hari.", ".$tanggal);
        $sheet->mergeCells('A2:C2');
        $sheet->getStyle('A2:C2')->applyFromArray($style_top2);
        $sheet->getStyle('A2:C2')->getFill()
          ->setFillType(Fill::FILL_SOLID)
          ->getStartColor()->setRGB('FFB43C');

        $sheet->setCellValue('A3', "PENJUALAN KOTOR");
        $sheet->setCellValue('C3', "Rp ".number_format($total_penjualan_kotor, 0, ',', '.'));

        $sheet->getStyle('A3')->applyFromArray($style_col);
        $sheet->getStyle('B3')->applyFromArray($style_col);
        $sheet->getStyle('C3')->applyFromArray($style_col);
        $sheet->getStyle('A3:C3')->getFill()
          ->setFillType(Fill::FILL_SOLID)
          ->getStartColor()->setRGB('E3E3E3');

        $numrow = 4;
        
        $sheet->setCellValue('A'.$numrow, "MODAL");
        $sheet->setCellValue('C'.$numrow, "Rp ".number_format($total_modal, 0, ',', '.')." (+)");

        $sheet->getStyle('A'.$numrow)->applyFromArray($style_col);
        $sheet->getStyle('B'.$numrow)->applyFromArray($style_col);
        $sheet->getStyle('C'.$numrow)->applyFromArray($style_col);
        $numrow++;
        
        
        $sheet->setCellValue('A'.$numrow, "");
        $sheet->setCellValue('B'.$numrow, "");
        $sheet->setCellValue('C'.$numrow, "Rp ".number_format($total1, 0, ',', '.'));
        $sheet->getStyle('A'.$numrow)->applyFromArray($style_col);
        $sheet->getStyle('B'.$numrow)->applyFromArray($style_col);
        $sheet->getStyle('C'.$numrow)->applyFromArray($style_col);
        $numrow++;

        $sheet->setCellValue('A'.$numrow, "TAMBAHAN MODAL");
        $sheet->setCellValue('B'.$numrow, "");
        $sheet->setCellValue('C'.$numrow, "");
        $sheet->getStyle('A'.$numrow)->applyFromArray($style_col);
        $sheet->getStyle('B'.$numrow)->applyFromArray($style_col);
        $sheet->getStyle('C'.$numrow)->applyFromArray($style_col);
        $numrow++;

        foreach($modal_tambahan as $th){
            $sheet->setCellValue('A'.$numrow, "(+) ".$th->keterangan);
            $sheet->setCellValue('B'.$numrow, "Rp ".number_format($th->debit, 0, ',', '.')." (+)");

            $sheet->getStyle('A'.$numrow)->applyFromArray($style_col);
            $sheet->getStyle('B'.$numrow)->applyFromArray($style_col);
            $sheet->getStyle('C'.$numrow)->applyFromArray($style_col);

            $numrow++;
        }

        $sheet->setCellValue('A'.$numrow, "JUMLAH TAMBAHAN MODAL");
        $sheet->setCellValue('B'.$numrow, "");
        $sheet->setCellValue('C'.$numrow, "Rp ".number_format($total_modal_tambahan, 0, ',', '.')." (+)");
        $sheet->getStyle('A'.$numrow)->applyFromArray($style_col);
        $sheet->getStyle('B'.$numrow)->applyFromArray($style_col);
        $sheet->getStyle('C'.$numrow)->applyFromArray($style_col);
        $numrow++;

        $sheet->setCellValue('A'.$numrow, "LABA KOTOR");
        $sheet->setCellValue('B'.$numrow, "");
        $sheet->setCellValue('C'.$numrow, "Rp ".number_format($laba_kotor, 0, ',', '.'));
        $sheet->getStyle('A'.$numrow)->applyFromArray($style_col);
        $sheet->getStyle('B'.$numrow)->applyFromArray($style_col);
        $sheet->getStyle('C'.$numrow)->applyFromArray($style_col);
        $sheet->getStyle('A'.$numrow.':C'.$numrow)->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setRGB('E3E3E3');
        $numrow++;

        $sheet->setCellValue('A'.$numrow, "PENGURANGAN/PENGELUARAN");
        $sheet->setCellValue('B'.$numrow, "");
        $sheet->setCellValue('C'.$numrow, "");
        $sheet->getStyle('A'.$numrow)->applyFromArray($style_col);
        $sheet->getStyle('B'.$numrow)->applyFromArray($style_col);
        $sheet->getStyle('C'.$numrow)->applyFromArray($style_col);
        $numrow++;

        foreach($keluar as $th){
            $sheet->setCellValue('A'.$numrow, "(-) ".$th->keterangan);
            $sheet->setCellValue('B'.$numrow, "Rp ".number_format($th->kredit, 0, ',', '.')." (+)");

            $sheet->getStyle('A'.$numrow)->applyFromArray($style_col);
            $sheet->getStyle('B'.$numrow)->applyFromArray($style_col);
            $sheet->getStyle('C'.$numrow)->applyFromArray($style_col);

            $numrow++;
        }

        $sheet->setCellValue('A'.$numrow, "JUMLAH PENGURANGAN/PENGELUARAN");
        $sheet->setCellValue('B'.$numrow, "");
        $sheet->setCellValue('C'.$numrow, "Rp ".number_format($total_keluar, 0, ',', '.')." (-)");
        $sheet->getStyle('A'.$numrow)->applyFromArray($style_col);
        $sheet->getStyle('B'.$numrow)->applyFromArray($style_col);
        $sheet->getStyle('C'.$numrow)->applyFromArray($style_col);
        $numrow++;

        $sheet->setCellValue('A'.$numrow, "LABA BERSIH");
        $sheet->setCellValue('B'.$numrow, "");
        $sheet->setCellValue('C'.$numrow, "Rp ".number_format($laba_bersih, 0, ',', '.'));
        $sheet->getStyle('A'.$numrow)->applyFromArray($style_col);
        $sheet->getStyle('B'.$numrow)->applyFromArray($style_col);
        $sheet->getStyle('C'.$numrow)->applyFromArray($style_col);
        $sheet->getStyle('A'.$numrow.':C'.$numrow)->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setRGB('E3E3E3');
        $numrow++;

        $sheet->setCellValue('A'.$numrow, "(-) MODAL HARI INI");
        $sheet->setCellValue('C'.$numrow, "Rp ".number_format($total_modal_darurat, 0, ',', '.')." (-)");

        $sheet->getStyle('A'.$numrow)->applyFromArray($style_col);
        $sheet->getStyle('B'.$numrow)->applyFromArray($style_col);
        $sheet->getStyle('C'.$numrow)->applyFromArray($style_col);
        $numrow++;

        $sheet->setCellValue('A'.$numrow, "TOTAL TRANSFER/SETOR TUNAI");
        $sheet->setCellValue('B'.$numrow, "");
        $sheet->setCellValue('C'.$numrow, "Rp ".number_format($total_transfer, 0, ',', '.'));
        $sheet->getStyle('A'.$numrow)->applyFromArray($style_col);
        $sheet->getStyle('B'.$numrow)->applyFromArray($style_col);
        $sheet->getStyle('C'.$numrow)->applyFromArray($style_col);
        $sheet->getStyle('A'.$numrow.':C'.$numrow)->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setRGB('FFC531');
        $numrow++;

        $sheet->getColumnDimension('A')->setWidth(45); // Set width kolom A
        $sheet->getColumnDimension('B')->setWidth(25); // Set width kolom B
        $sheet->getColumnDimension('C')->setWidth(25);

        $sheet->getDefaultRowDimension()->setRowHeight(-1);
        // Set orientasi kertas jadi LANDSCAPE
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        // Set judul file excel nya
        $sheet->setTitle("Laporan Data Siswa");
        // Proses file excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Laporan Laba Rugi.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }
}
