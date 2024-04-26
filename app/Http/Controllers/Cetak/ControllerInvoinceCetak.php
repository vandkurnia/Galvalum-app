<?php

namespace App\Http\Controllers\Cetak;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ControllerInvoinceCetak extends Controller
{
    public function print_invoice()
    {
        $data['title'] = 'Print Invoice';

        $dataPembeli = [
            [
                "nama" => "Haasstt",
                "alamat" => "Ds. Lebak Ayu Kec. Sawahan, Kab. Madiun",
                "telp" => "09832324323"
            ]
        ];
        $dataNota = [
            [
                "tanggal" => "14-Nov-24",
                "no_nota" => "JL00001001"
            ]
        ];

        $namaKasir = "Sasa";

        $dataPembayaran = [
            [
                "termin" => "-",
                "jatuh_tempo" => "-"
            ]
        ];


        $productsData = [
            [
                "item" => "001222",
                "deskripsi" => "Dancow Cokelat 400gr",
                "qty" => "4",
                "unit" => "DUS",
                "harga" => "27,000",
                "disc" => "-",
                "subtotal" => "108,000"
            ],
            [
                "item" => "001225",
                "deskripsi" => "Dancow Full Cream 400gr",
                "qty" => "4",
                "unit" => "DUS",
                "harga" => "26,500",
                "disc" => "-",
                "subtotal" => "106,000"
            ],
            [
                "item" => "001230",
                "deskripsi" => "Dancow 5+ Coklat 800gr",
                "qty" => "2",
                "unit" => "DUS",
                "harga" => "57,000",
                "disc" => "-",
                "subtotal" => "114,000"
            ]
        ];

        $rincian = [
            'subtotalHarga' => '328.000',
            'diskon' => '0',
            'pajak' => '0',
            'total' => '328.000',
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
    public function print_suratJalan()
    {
        $data['title'] = 'Print Surat Jalan';

        $dataPembeli = [
            [
                "nama" => "Haasstt",
                "alamat" => "Ds. Lebak Ayu Kec. Sawahan, Kab. Madiun",
                "telp" => "09832324323"
            ]
        ];

        $dataSuratJalan = [
            [
                "tanggal" => "14-Nov-24",
                "no_surat" => "JL00001001"
            ]
        ];


        $productsData = [
            [
                "nama_barang" => "Dancow Cokelat 400gr",
                "qty" => "4"
            ],
            [
                "nama_barang" => "Dancow Full Cream 400gr",
                "qty" => "4"
            ],
            [
                "nama_barang" => "Dancow 5+ Coklat 800gr",
                "qty" => "2"
            ]
        ];

        return view('pdfprint.surat-jalan', [
            'dataPembeli' => $dataPembeli,
            'dataSuratJalan' => $dataSuratJalan,
            'dataRincianBarang' => $productsData
        ], $data);
    }
}
