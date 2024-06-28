<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        /* FOrmat Body */
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            /* margin: 0; */
            /* padding: 5px 5px;
            margin: 10px 10px 10px 10px; */
            /* Center the content horizontally and vertically */
            display: flex;
            flex-direction: column;
            /* justify-content: center; */
            align-items: center;
            min-height: 95vh;
            min-width: 80vw;
            box-sizing: border-box;

        }

        h1 {
            font-family: 'Times New Roman', Times, serif;
            font-size: 32px;
            font-weight: bold;

        }

        h2 {
            font-family: 'Times New Roman', Times, serif;
            font-size: 14px;
            font-weight: bold;
            padding: 1px 2px 2px 5px;
        }

        p {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            font-weight: 300;
            padding: 1px 1px 5px 5px;
        }

        /* End format body */
        /* Header */




        /* End of kosong */
        /* Box Border */
        .box-border {

            flex-grow: 1;
        }

        .box-border-top {}



        /* End Box Border */
        /* Info pembeli dan penjual */
        /* .info-pembeli-dan-penjual {
            max-width: 400px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            padding: 0px 5px 10px 5px;

        } */

        .informasi-surat-lain {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 0px 5px 10px 5px;
            max-width: 400px;
            flex-grow: 1;
        }

        .informasi-surat-lain table {
            flex-grow: 1;
        }

        .informasi-pembeli {
            border: 1px solid black;
            margin-bottom: 10px;
            flex-grow: 1;
        }

        .label-pembeli {
            font-weight: bold;
        }

        .nama-pembeli {
            /* border-top: 1px solid black; */
            /* margin-top: 10px; */
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            padding: 10px 5px 5px 10px;
            min-height: 100px;
        }


        .nama-pembeli span {
            padding-bottom: 1rem;
        }

        /* Flex */

        /* Gaya tabel */
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            text-align: left;
            padding: 2px;
            border: 1px solid #000;
        }

        th {
            background-color: #f0f0f0;
        }

        /* Gaya header */
        /* .header {
            background-color: #333;
            color: white;
            padding: 10px 0;
            text-align: center;
        } */

        /* Gaya footer */
        .footer {
            background-color: #f0f0f0;
            padding: 10px 0;
            text-align: right;
        }
    </style>
    <style>
        /* Bagian Logo */
        .informasi-nota {
            /* border: 2px solid yellow; */
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            padding: 10px;
            box-sizing: border-box;
        }

        .logo-toko,
        .informasi-toko {
            width: 50%;
            /* height: 20%; */
        }

        .logo-toko img {
            margin: 5;
            width: 75%;
            height: auto;
            border-radius: 20%;
        }

        .nama-toko {
            font-weight: bold;
        }

        /* .alamat-toko {
            width: 40px;
        } */

        .nomor-telepon-toko {
            margin-top: 0;
        }
    </style>
</head>

<body onload="window.print()" style="position: relative">
    <header style="position: relative; height: 280px;">
        <div class="info-pembeli-dan-penjual" style="position:absolute; height:350px; ">
            <div class="informasi-nota" style="width: 300px; position: relative;">
                <div class="logo-toko" style="position: absolute; width: 120px; top: 0;">
                    <img src="{{ secure_asset('assets/logogalvalum.jpg') }}" alt="Logo Galvalum">
                </div>

                {{-- <div class="informasi-toko" style="position:absolute;left: 125px;  width: 199px;"> --}}
                <div class="informasi-toko" style="position:absolute;left: 125px;  width: 60%; top: 0;">
                    <h2 class="nama-toko" style="font-size: 14px;">TK. BIMA SAKTI PERKASA</h2>
                    <p class="alamat-toko"
                        style="
                        margin-bottom: 0px;
                        padding-bottom: 2px;">
                        Jl. Raya Barat
                        Jiwan, Utara Lampu Merah Madiun</p>
                    <p class="alamat-toko"
                        style="
                        margin-top: 0px;
                        padding-top: 0px;
                        ">
                        Wa
                        / Telp : 085733823107
                    </p>

                    <p class="nomor-telepon-toko"></p>
                </div>
            </div>
            <table class="table" style="position: absolute; left: 0; top: 125px;">
                <thead>
                    <tr>
                        <th class="text-center">Kepada Yth.:</th>
                    </tr>
                </thead>
                <tbody class="text-dark" style="background-color: #e9ecef">
                    <tr class="fw-light">
                        <td class="nama-pembeli" style="font-size: 14px; height:20px;">
                            @foreach ($dataPembeli as $index => $data)
                                <span style="margin-bottom: 5px;">{{ $data['nama'] }}<br><br></span>
                                <span style="margin-bottom: 5px;">{{ $data['alamat'] }} <br><br></span>
                                <span style="margin-bottom: 5px;">{{ $data['telp'] }}</span>
                            @endforeach
                        </td>
                    </tr>
                </tbody>
            </table>
            <!-- <div class="informasi-pembeli ">
                <h2 class="label-pembeli">Kepada Yth.:</h2>
                <div class="nama-pembeli">
                    <span>Ibu Dewi</span>
                    <span>Desa Lebak Ayu Sawahan Madiun</span>
                    <span>0895359508913</span>
                </div>

            </div> -->
        </div>


        <div class="informasi-surat-lain" style="position: absolute; right:0; width:300px; height:320px;">
            <div style="position: relative; height:280px;">
                <h1 style="margin-top: 0;">
                    Nota Penjualan
                </h1>
                <table class="table" style="margin-top: 5px;">
                    <thead>
                        <tr>
                            <th style="width: 30%;">Tanggal</th>
                            <th class="text-center">Nomor Nota</th>
                        </tr>
                    </thead>
                    <tbody class="text-dark" style="background-color: #e9ecef">
                        <tr class="fw-light" style="font-size: 14px;">
                            @foreach ($dataNota as $data)
                                <td class="text-center">{{ $data['tanggal'] }}</td>
                                <td class="text-center" style="height: 59px;">{{ $data['no_nota'] }} </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
                <table class="table" style="margin-top: 28px; height: 100px;">
                    <thead>
                        <tr>
                            <th class="text-center">Admin/Kasir</th>
                        </tr>
                    </thead>
                    <tbody class="text-dark" style="background-color: #e9ecef">
                        <tr class="fw-light">
                            <td class="text-center" style="font-size: 14px; height: 59px;">{{ $dataKasir }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </header>
    <main style="position: relative;">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    {{-- <th>Item</th> --}}
                    <th>Deskripsi</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Harga</th>
                    {{-- <th>Disc</th> --}}
                    <th>Total Harga</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dataRincianBarang['list_barang'] as $index => $data)
                    <tr>
                        <td style="width: 20px;">{{ $index + 1 }}</td>
                        {{-- <td style="width: 50px;">{{ $data['item'] }}</td> --}}
                        <td style="height:10px;">{{ $data['deskripsi'] }}</td>
                        <td style="width: 60px;">{{ (int) $data['qty'] }}</td>
                        <td>{{ $data['pesanan'] }}</td>
                        <td style="width: 100px;">Rp. {{ number_format($data['harga'], 0, ',', '.') }}</td>
                        {{-- <td>{{ $data['disc'] }}</td> --}}
                        <td style="width: 100px;">Rp. {{ number_format($data['subtotal'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach

            </tbody>
            @if ($dataRincianBarang['status'] == 'hutang')
                <tfoot>
                    <tr>
                        <td rowspan="5" colspan="4">
                            <h2>Keterangan</h2>
                            <ol>
                                <li>Barang yang sudah dibeli tidak bisa ditukar / dikembalikan</li>
                                <li>BRI : 388401024665532 / An. Budiono</li>
                                <li>BCA : 1771837226 / An. Budiono</li>
                            </ol>
                        </td>
                        <td colspan="1" style="height:10px;">Subtotal</td>
                        <td colspan="1">Rp. {{ number_format($dataRincianBarang['subtotalHarga'], 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="1">Diskon</td>
                        <td colspan="1">Rp. {{ number_format($dataRincianBarang['diskon'], 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="1">Ongkir</td>
                        <td colspan="1">Rp. {{ number_format($dataRincianBarang['ongkir'], 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="1">Dp</td>
                        <td colspan="1">Rp. {{ number_format($dataRincianBarang['dp'], 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="1">Total</td>
                        <td colspan="1">Rp. {{ number_format($dataRincianBarang['total'] - $dataRincianBarang['dp'], 0, ',', '.') }}</td>
                    </tr>

                </tfoot>
            @else
                <tfoot>
                    <tr>
                        <td rowspan="4" colspan="4">
                            <h2>Keterangan</h2>
                            <ol>
                                <li>Barang yang sudah dibeli tidak bisa ditukar / dikembalikan</li>
                                <li>BRI : 388401024665532 / An. Budiono</li>
                                <li>BCA : 1771837226 / An. Budiono</li>
                            </ol>
                        </td>
                        <td colspan="1" style="height:10px;">Subtotal</td>
                        <td colspan="1">Rp. {{ number_format($dataRincianBarang['subtotalHarga'], 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="1">Diskon</td>
                        <td colspan="1">Rp. {{ number_format($dataRincianBarang['diskon'], 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="1">Ongkir</td>
                        <td colspan="1">Rp. {{ number_format($dataRincianBarang['ongkir'], 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="1">Total</td>
                        <td colspan="1">Rp. {{ number_format($dataRincianBarang['total'], 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            @endif





        </table>
        {{-- <div class="keterangan-tambahan" style="
    text-align: right;">
            <p>* Harga termasuk pajak</p>
        </div> --}}


    </main>




</body>

</html>
