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
        /* Tampilan Table laba rugi */
        table thead {
            text-align: center;
            background-color: #FFB43C;
        }

        .topic {
            font-weight: bold;
            background-color:#E3E3E3;
        }
    </style>
</head>
<body>
<div class="card-body" style="width: 100%;">

<div class="table-responsive" style="width: 100%;">
    <table class="table table-border" style="width: 100%;" border="1" cellspacing="0" cellpadding="5">
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
            <tr>
                <td>MODAL</td>
                <td></td>

                <td><center>Rp {{ number_format($total_modal, 0, ',', '.') }} (+)</center></td>
            </tr>
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
            <tr>
                <th style="text-align: left;">(-) MODAL HARI INI</th>
                <th></th>
                <th>Rp {{ number_format($total_modal_darurat, 0, ',', '.') }} (-)</th>
            </tr>
            <tr style="background-color: #FFC531;">
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