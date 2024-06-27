<?php

namespace App\Http\Controllers\Cetak;

use App\Http\Controllers\Controller;
use App\Models\NotaPembeli;
use App\Models\pdf\InvoicePembayaranModel;
use App\Models\pdf\SuratJalanModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mpdf\Mpdf;
use TCPDF;

class ControllerInvoinceCetak extends Controller
{
    public function print_invoice($no_nota)
    {
        $data['title'] = 'Print Invoice ' . $no_nota;
        $notaPembeli = NotaPembeli::with('Pembeli', 'Admin', 'PesananPembeli', 'PesananPembeli.Barang', 'bukuBesar', 'Admin')->where('no_nota', $no_nota)->first();


        // Membuat instance baru dari model InvoicePembelian
        $invoiceCetak = new InvoicePembayaranModel();

        // Mengatur properti model
        $invoiceCetak->users = Auth::user()->id_admin;   // Ganti dengan id_admin yang valid dari tabel users
        $invoiceCetak->id_nota = $notaPembeli->id_nota;                      // Ganti dengan id_nota yang valid dari tabel nota_pembelis

        // Menyimpan data ke database
        $invoiceCetak->save();
        $dataPembeli = [
            [
                "nama" => $notaPembeli->Pembeli->nama_pembeli,
                "alamat" => $notaPembeli->Pembeli->alamat_pembeli,
                "telp" => $notaPembeli->Pembeli->no_hp_pembeli
            ]
        ];
        // $dataPembeli = [
        //     [
        //         "nama" => "Haasstt",
        //         "alamat" => "Ds. Lebak Ayu Kec. Sawahan, Kab. Madiun",
        //         "telp" => "09832324323"
        //     ]
        // ];
        // $dataPembeli = ;
        $dataNota = [
            [
                "tanggal" => date("Y-m-d", strtotime($notaPembeli->created_at)),
                "no_nota" => $notaPembeli->no_nota
            ]
        ];
        // $dataNota = [
        //     [
        //         "tanggal" => "14-Nov-24",
        //         "no_nota" => "JL00001001"
        //     ]
        // ];


        // $namaKasir = "Sasa";
        // Asli
        // $namaKasir = $notaPembeli->Admin->nama_admin;
        // Sementara
        // Seharusnya tiap cetak dicatat
        $namaKasir = $notaPembeli->Admin->nama_admin;
        // dd($namaKasir);

        $dataPembayaran = [
            [
                "termin" =>  $notaPembeli->status_pembayaran == "lunas" ? "-" : $notaPembeli->nominal_terbayar,
                "jatuh_tempo" =>  $notaPembeli->tenggat_bayar
            ]
        ];

        $productsData = [];
        foreach ($notaPembeli->pesananPembeli as $pesananPembeli) {

            $productsData[] =  [
                "item" => "001222",
                "deskripsi" => $pesananPembeli->Barang->nama_barang,
                "qty" => $pesananPembeli->jumlah_pembelian,
                "pesanan" => $pesananPembeli->Barang->TipeBarang->nama_tipe,
                "harga" => $pesananPembeli->harga,
                "disc" => $pesananPembeli->diskon,
                "subtotal" => ($pesananPembeli->harga - $pesananPembeli->diskon) * $pesananPembeli->jumlah_pembelian
            ];
        }
        // $productsData = [
        //     [
        //         "item" => "001222",
        //         "deskripsi" => "Dancow Cokelat 400gr",
        //         "qty" => "4",
        //         "unit" => "DUS",
        //         "harga" => "27,000",
        //         "disc" => "-",
        //         "subtotal" => "108,000"
        //     ],
        //     [
        //         "item" => "001225",
        //         "deskripsi" => "Dancow Full Cream 400gr",
        //         "qty" => "4",
        //         "unit" => "DUS",
        //         "harga" => "26,500",
        //         "disc" => "-",
        //         "subtotal" => "106,000"
        //     ],
        //     [
        //         "item" => "001230",
        //         "deskripsi" => "Dancow 5+ Coklat 800gr",
        //         "qty" => "2",
        //         "unit" => "DUS",
        //         "harga" => "57,000",
        //         "disc" => "-",
        //         "subtotal" => "114,000"
        //     ]
        // ];

        $rincian = [

            'subtotalHarga' => $notaPembeli->sub_total,
            'diskon' => $notaPembeli->diskon,
            'ongkir' => $notaPembeli->ongkir,
            'total' => $notaPembeli->total,
            // 'dp' => ($notaPembeli['bukuBesar'][0]['debit'] - $notaPembeli['bukuBesar'][0]['kredit']),
            'dp' => $notaPembeli->nominal_terbayar,
            'status' => $notaPembeli['total'] == $notaPembeli['nominal_terbayar'] ? 'lunas' : 'hutang',
            'list_barang' => $productsData
        ];
        $type = 2;

        switch ($type) {
            case 1:
                $data = [
                    'title' => 'Print Invoice ' . $no_nota,
                    'dataPembeli' => $dataPembeli,
                    'dataNota' => $dataNota,
                    'dataKasir' => $namaKasir,
                    'dataPembayaran' => $dataPembayaran,
                    'dataRincianBarang' => $rincian
                ];

                $pdf = Pdf::loadView('pdfprint.dompdf.invoice-penjualan', $data)->setOptions(['defaultFont' => 'sans-serif']);
                return $pdf->download('invoice.pdf');
                break;
            case 2:

                $data = [
                    'title' => 'Print Invoice ' . $no_nota,
                    'dataPembeli' => $dataPembeli,
                    'dataNota' => $dataNota,
                    'dataKasir' => $namaKasir,
                    'dataPembayaran' => $dataPembayaran,
                    'dataRincianBarang' => $rincian
                ];
                $pdf = Pdf::loadView('pdfprint.dompdf.invoice-penjualan', $data)->setOptions(
                    [
                        'defaultFont' => 'sans-serif',
                        'isRemoteEnabled' => true,
                        'isHtml5ParserEnabled' => true,
                        'sizeA4' => true
                    ]

                );

                return $pdf->stream();

                // create new PDF document

                break;

            default:
                return view('pdfprint.invoice-penjualan', [
                    'dataPembeli' => $dataPembeli,
                    'dataNota' => $dataNota,
                    'dataKasir' => $namaKasir,
                    'dataPembayaran' => $dataPembayaran,
                    'dataRincianBarang' => $rincian
                ], $data);
                break;
        }
    }
    public function print_suratJalan($no_nota)
    {

        $notaPembeliModel = NotaPembeli::with('Pembeli', 'Admin', 'PesananPembeli', 'PesananPembeli.Barang')->where('no_nota', $no_nota)->first();
        $suratJalanFirst = SuratJalanModel::firstOrCreate(['id_nota' => $notaPembeliModel->id_nota, 'users' => Auth::user()->id_admin], ['id_nota' => $notaPembeliModel->id_nota,  'users' => Auth::user()->id_admin]);
      
        $title = 'Print Surat Jalan ' . $no_nota;
        $dataPembeli = [
            [
                "nama" => $notaPembeliModel->Pembeli->nama_pembeli,
                "alamat" => $notaPembeliModel->Pembeli->alamat_pembeli,
                "telp" => $notaPembeliModel->Pembeli->no_hp_pembeli
            ]
        ];

        $dataSuratJalan = [
            [
                "tanggal" => date("Y-m-d", strtotime($suratJalanFirst->created_at)),
                "no_surat" => $suratJalanFirst->no_surat_jalan
            ]
        ];

        // $dataAdmin = [
        //     'nama_admin' => $notaPembeliModel->Admin->nama_admin
        // ];
        // $dataSuratJalan = [
        //     [
        //         "tanggal" => "14-Nov-24",
        //         "no_surat" => "JL00001001"
        //     ]
        // ];


        $productsData = [];
        foreach ($notaPembeliModel->pesananPembeli as $pesananPembeli) {

            $productsData[] =     [
                "nama_barang" => $pesananPembeli->Barang->nama_barang,
                "qty" => $pesananPembeli->jumlah_pembelian,
            ];
        }



        $data = [
            'title' => $title,
            'dataPembeli' => $dataPembeli,
            'dataSuratJalan' => $dataSuratJalan,
            'dataRincianBarang' => $productsData,
            // 'dataAdmin' => $dataAdmin
        ];


        $type = 2;

        switch ($type) {
            case 1:

                $pdf = Pdf::loadView('pdfprint.dompdf.surat-jalan', $data)->setOptions(['defaultFont' => 'sans-serif']);
                return $pdf->download('invoice.pdf');
                break;
            case 2:


                $pdf = Pdf::loadView('pdfprint.dompdf.surat-jalan', $data)->setOptions(
                    [
                        'defaultFont' => 'sans-serif',
                        'isRemoteEnabled' => true,
                        'isHtml5ParserEnabled' => true,
                        'sizeA4' => true
                    ]

                );

                return $pdf->stream();

                // create new PDF document

                break;

            default:
                return view('pdfprint.surat-jalan', [
                    'dataPembeli' => $dataPembeli,
                    'dataSuratJalan' => $dataSuratJalan,
                    'dataRincianBarang' => $productsData,
                    // 'dataAdmin' => $dataAdmin
                ], $data);
                break;
        }


        // $pdf = Pdf::loadView('pdfprint.surat-jalan', $data)->setOptions(['defaultFont' => 'sans-serif']);
        // return $pdf->download('invoice.pdf');
        // return $pdf->stream(); -->
    }
}
