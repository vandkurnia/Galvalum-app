<?php

namespace App\Http\Controllers\Cetak;

use App\Http\Controllers\Controller;
use App\Models\NotaPembeli;
use Illuminate\Http\Request;

class ControllerInvoinceCetak extends Controller
{
    public function print_invoice($no_nota)
    {
        $data['title'] = 'Print Invoice ' . $no_nota;
        $notaPembeliModel = NotaPembeli::with('Pembeli', 'Admin', 'PesananPembeli', 'PesananPembeli.Barang')->where('no_nota', $no_nota)->first();

        $dataPembeli = [
            [
                "nama" => $notaPembeliModel->Pembeli->nama_pembeli,
                "alamat" => $notaPembeliModel->alamat_pembeli,
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
            'list_barang' => $productsData
        ];

        return view('pdfprint.invoice-penjualan', [
            'dataPembeli' => $dataPembeli,
            'dataNota' => $dataNota,
            'dataKasir' => $namaKasir,
            'dataPembayaran' => $dataPembayaran,
            'dataRincianBarang' => $rincian
        ], $data);
    }
    public function print_suratJalan($no_nota)
    {

        $notaPembeliModel = NotaPembeli::with('Pembeli', 'Admin', 'PesananPembeli', 'PesananPembeli.Barang')->where('no_nota', $no_nota)->first();

        $data['title'] = 'Print Surat Jalan' . $no_nota;
        $dataPembeli = [
            [
                "nama" => $notaPembeliModel->Pembeli->nama_pembeli,
                "alamat" => $notaPembeliModel->alamat_pembeli,
                "telp" => $notaPembeliModel->Pembeli->no_hp_pembeli
            ]
        ];

        $dataSuratJalan = [
            [
                "tanggal" => date("Y-m-d", strtotime($notaPembeliModel->created_at)),
                "no_surat" => $notaPembeliModel->no_nota
            ]
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

        return view('pdfprint.surat-jalan', [
            'dataPembeli' => $dataPembeli,
            'dataSuratJalan' => $dataSuratJalan,
            'dataRincianBarang' => $productsData
        ], $data);
    }
}
