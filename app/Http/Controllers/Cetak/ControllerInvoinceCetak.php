<?php

namespace App\Http\Controllers\Cetak;

use App\Http\Controllers\Controller;
use App\Models\NotaPembeli;
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
        $notaPembeliModel = NotaPembeli::with('Pembeli', 'Admin', 'PesananPembeli', 'PesananPembeli.Barang', 'bukuBesar')->where('no_nota', $no_nota)->first();
     

        $dataPembeli = [
            [
                "nama" => $notaPembeliModel->Pembeli->nama_pembeli,
                "alamat" => $notaPembeliModel->Pembeli->alamat_pembeli,
                "telp" => $notaPembeliModel->Pembeli->no_hp_pembeli
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
                "tanggal" => date("Y-m-d", strtotime($notaPembeliModel->created_at)),
                "no_nota" => $notaPembeliModel->no_nota
            ]
        ];
        // $dataNota = [
        //     [
        //         "tanggal" => "14-Nov-24",
        //         "no_nota" => "JL00001001"
        //     ]
        // ];


        // $namaKasir = "Sasa";
        $namaKasir = $notaPembeliModel->Admin->nama_admin;

        $dataPembayaran = [
            [
                "termin" =>  $notaPembeliModel->status_pembayaran == "lunas" ? "-" : $notaPembeliModel->nominal_terbayar,
                "jatuh_tempo" =>  $notaPembeliModel->tenggat_bayar
            ]
        ];

        $productsData = [];
        foreach ($notaPembeliModel->pesananPembeli as $pesananPembeli) {

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

            'subtotalHarga' => $notaPembeliModel->sub_total,
            'diskon' => $notaPembeliModel->diskon,
            'ongkir' => $notaPembeliModel->ongkir,
            'total' => $notaPembeliModel->total,
            'dp' => ($notaPembeliModel['bukuBesar'][0]['debit'] - $notaPembeliModel['bukuBesar'][0]['kredit']),
            'status' => $notaPembeliModel['total'] == $notaPembeliModel['nominal_terbayar'] ? 'lunas' : 'hutang',
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
        $suratJalanModel = SuratJalanModel::firstOrCreate(['id_nota' => $notaPembeliModel->id_nota], ['id_nota' => $notaPembeliModel->id_nota]);

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
                "tanggal" => date("Y-m-d", strtotime($suratJalanModel->created_at)),
                "no_surat" => $suratJalanModel->no_surat_jalan
            ]
        ];

        $dataAdmin = [
            'nama_admin' => $notaPembeliModel->Admin->nama_admin
        ];
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
            'dataAdmin' => $dataAdmin
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
                    'dataAdmin' => $dataAdmin
                ], $data);
                break;
        }


        // $pdf = Pdf::loadView('pdfprint.surat-jalan', $data)->setOptions(['defaultFont' => 'sans-serif']);
        // return $pdf->download('invoice.pdf');
        // return $pdf->stream(); -->
    }
}
