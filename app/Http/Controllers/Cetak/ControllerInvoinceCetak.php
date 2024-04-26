<?php

namespace App\Http\Controllers\Cetak;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ControllerInvoinceCetak extends Controller
{
    public function print_invoice()
    {
        $data['title'] = 'Print Invoicehhhhh';

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


        $dummyData = [
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
            'list_barang' => $dummyData
        ];

        return view('pdfprint.invoice-penjualan', [
            'dataPembeli' => $dataPembeli,
            'dataNota' => $dataNota,
            'dataKasir' => $namaKasir,
            'dataPembayaran' => $dataPembayaran,
            'dataRincianBarang' => $rincian
        ], $data);
    }
}
