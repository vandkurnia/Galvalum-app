<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laba Rugi</title>
    <style>
        /* Sembunyikan Showing entries */
        #dataTable_length,
        #dataTable_info {
            display: none;
        }

        /* Sembunyikan pagination */
        #dataTable_paginate {
            display: none;
        }

        .table {
            background-color : #dcdcdc;
        }


        /* Tampilan Table laba rugi */
        table thead {
            text-align: center;
            background-color: #7091e6;
            color: white;
        }

        .topic {
            font-weight: bold;
            background-color: grey;
            color: white;
        }
    </style>
</head>
<body>
<div class="card-body" style="width: 100%;">

<div class="table-responsive" style="width: 100%;">
    <table class="table table-border" style="width: 100%;">
        <thead>
            <tr>
                <th colspan="3"> REKAP RINCIAN PENJUALAN </th>
            </tr>
            <tr>
                <th colspan="3">{{ $hari }}, {{ $tanggal }}</th>
            </tr>
        </thead>
        <tbody>

            
            <tr class="topic">
                <th style="text-align: left;">PENJUALAN KOTOR</th>
                <td></td>

                <td><center>Rp {{ number_format($total_penjualan_kotor, 0, ',', '.') }}</center></td>
            </tr>
            
            @foreach($modal as $m)
            <tr>
                <td>MODAL</td>
                <td></td>

                <td><center>Rp {{ number_format($m->debit, 0, ',', '.') }} (+)</center></td>
            </tr>
            @endforeach
            <tr>
                <th></th>
                <th></th>
                <th>Rp {{ number_format($total1, 0, ',', '.') }}</th>
            </tr>
            <tr>
                <th style="text-align: left;">TAMBAHAN MODAL</th>
                <td></td>
                <td></td>
            </tr>
            @foreach($modal_tambahan as $th)
            <tr>
                <td>(+) {{ $th->keterangan }}</td>
                <td>Rp {{ number_format($th->debit, 0, ',', '.') }} (+)</td>
                <td></td>
            </tr>
            @endforeach
            <tr>
                <th style="text-align: left;">JUMLAH TAMBAHAN MODAL</th>
                <td></td>
                <td><center>Rp {{ number_format($total_modal_tambahan, 0, ',', '.') }} (+)</center></td>
            </tr>
            <tr class="topic">
                <th style="text-align: left;">LABA KOTOR</th>
                <th></th>
                <th>Rp {{ number_format($laba_kotor, 0, ',', '.') }}</th>
            </tr>
            <tr>
                <th style="text-align: left;">PENGURANGAN/PENGELUARAN</th>
                <td></td>
                <td></td>
            </tr>
            @foreach($keluar as $p)
            <tr>
                <td>(-) {{$p->keterangan}}</td>
                <td>Rp {{ number_format($p->kredit, 0, ',', '.') }} (+)</td>
                <td></td>
            </tr>
            @endforeach
            <tr>
                <th style="text-align: left;">JUMLAH PENGURANGAN/PENGELUARAN</th>
                <th></th>
                <th>Rp {{ number_format($total_keluar, 0, ',', '.') }} (-)</th>
            </tr>
            <tr class="topic">
                <th style="text-align: left;">LABA BERSIH</th>
                <th></th>
                <th>Rp {{ number_format($laba_bersih, 0, ',', '.') }}</th>
            </tr>
            @foreach($modal_darurat as $md)
            <tr>
                <th style="text-align: left;">(-) MODAL HARI INI</th>
                <th></th>
                <th>Rp {{ number_format($md->kredit, 0, ',', '.') }} (-)</th>
            </tr>
            @endforeach
            <tr>
                <th style="text-align: left;">TOTAL TRANSFER/SETOR TUNAI</th>
                <th></th>
                <th>Rp {{ number_format($total_transfer, 0, ',', '.') }}</th>
            </tr>



        </tbody>
    </table>

</div>
</div>
    </table>
</body>
</html>