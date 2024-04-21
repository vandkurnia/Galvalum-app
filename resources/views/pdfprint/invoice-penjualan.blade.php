<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Belanja</title>
    <style>
        /* FOrmat Body */
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            /* margin: 0; */
            padding: 5px 5px;
            margin: 10px 10px 10px 10px;
            /* Center the content horizontally and vertically */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            min-width: 80vw;
            border: 1px dotted black;
            /* Set background color or image */
            background-color: #fff;
            /* Change with your desired background color */
            /* Optionally, set a background image */
            /* background-image: url("https://www.pexels.com/search/background/"); */
            /* background-size: cover;  /* Adjust as needed */
            /* background-position: center;  /* Adjust as needed */
            /* display: grid;
            grid-template-columns: 20% auto  20%;
            grid-template-rows: 20% 20% 20% auto;
            grid-template-areas:
            "logotoko kosong invoicepenjualan"
            "infopembeli kosong adminkasir"
            "infopembeli kosong detailnota"
            "pesanan pesanan pesanan"; */
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
        header {
            display: flex;
            /* border: 1px solid black; */
            gap: 60px;
            /* Jarak antara item-flex dalam container */
            min-width: 130px;
            width: 100%;
            justify-content: space-between;
        }



        /* End of kosong */
        /* End Header */
        /* Box Border */
        .box-border {
            border: 1px solid black;
            flex-grow: 1;
        }

        .box-border-top {
            border-top: 1px solid black;
        }



        /* End Box Border */
        /* Info pembeli dan penjual */
        .info-pembeli-dan-penjual {
            max-width: 400px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            padding: 0px 5px 0px 5px;
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
            border-top: 1px solid black;
            margin-top: 10px;
            box-sizing: border-box;
            display: flex;
            padding: 0px 5px 5px 5px;
            min-height: 100px;
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
            padding: 8px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f0f0f0;
        }

        /* Gaya header */
        .header {
            background-color: #333;
            color: white;
            padding: 10px 0;
            text-align: center;
        }

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
            margin-right: 68px;
        }

        .logo-toko,
        .informasi-toko {
            width: 50%;
            /* height: 20%; */
        }

        .logo-toko img {
            width: 100%;
            height: auto;
        }

        .nama-toko {
            font-weight: bold;
        }

        .alamat-toko {
            width: 40px;
        }

        .nomor-telepon-toko {
            margin-top: 0;
        }
    </style>
</head>

<body>
    <header>
        <div class="info-pembeli-dan-penjual">
            <div class="informasi-nota">
                <div class="logo-toko">
                    <img src="https://aqbgvzzymp.cloudimg.io/v7/barokahabadi.co.id/wp-content/uploads/2020/11/dummy-logo-1b.png"
                        alt="Logo Beer Bee">
                </div>



                <div class="informasi-toko">
                    <h2 class="nama-toko">PT Bima Sakti</h2>
                    <p class="alamat-toko">Magetan</p>
                    <p class="nomor-telepon-toko">Telp: 03183103; Fax</p>
                </div>




            </div>

            <div class="informasi-pembeli ">
                <h2 class="label-pembeli">Kepada Yth.:</h2>
                <div class="nama-pembeli">
                    <p>Ibu Dewi</p>
                </div>

            </div>
        </div>

        <div class="informasi-surat-lain"
            style="
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding: 0px 5px 10px 5px;
    max-width: 400px;
    flex-grow: 1;
">
            <h1>
                Invoice Penjualan
            </h1>

            <div class="detail-nota box-border" style="
    display: flex;
">
                <div class="tanggal-nota" style="
    flex-grow: 1;
">
                    <h2>Tanggal</h2>
                    <div class="isi-tanggal box-border-top">
                        <p> 14-Nov-15</p>
                    </div>
                </div>
                <div class="nomor-nota" style="
    flex-grow: 1;
    border-left: 1px solid black;
">
                    <h2>No.Nota</h2>
                    <div class="isi-nomor-nota" style="1px
    solid color: black;
    border-top: 1px solid black;
">
                        <p> JL00001001</p>
                    </div>
                </div>

            </div>
            <div class="admin-kasir box-border">
                <h2>Admin / Kasir</h2>
                <div class="box-border-top">
                    <p>Sasa</p>
                </div>
            </div>
            <div class="detail-nota" style="
    display: flex;
">
                <div class="termin-pembayaran box-border" style="
    flex-grow: 1;
">
                    <h2>Termin</h2>
                    <div class="isi-termin box-border-top">
                        <p> 200000</p>
                    </div>
                </div>
                <div class="jatuh-tempo box-border" style="
    margin: 0;
">
                    <h2>Jatuh Tempo</h2>
                    <div class="tanggal-jatuh-tempo box-border-top">
                        <p> 14-Nov-15</p>
                    </div>
                </div>

            </div>
        </div>
    </header>
    <main style="
    width: 100%;
">


        <table>
            <tbody>
                <tr>
                    <th>No</th>
                    <th>Item</th>
                    <th>Deskripsi</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Harga</th>
                    <th>Disc</th>
                    <th>Subtotal</th>
                </tr>
                <tr>
                    <td>1</td>
                    <td>001222</td>
                    <td>Dancow Cokelat 400gr</td>
                    <td>4</td>
                    <td>DUS</td>
                    <td>27,000</td>
                    <td>10%</td>
                    <td>108,000</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>001225</td>
                    <td>Dancow Full Cream 400gr</td>
                    <td>4</td>
                    <td>DUS</td>
                    <td>26,500</td>
                    <td>10%</td>
                    <td>106,000</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>001230</td>
                    <td>Dancow 5+ Coklat 800gr</td>
                    <td>2</td>
                    <td>DUS</td>
                    <td>57,000</td>
                    <td>10%</td>
                    <td>114,000</td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>001231</td>
                    <td>Dancow 5+ Vanila 800gr</td>
                    <td>5</td>
                    <td>DUS</td>
                    <td>57,000</td>
                    <td>10%</td>
                    <td>285,000</td>
                </tr>
                <tr>
                    <td>5</td>
                    <td>001232</td>
                    <td>Dancow 5+ Madu 800gr</td>
                    <td>8</td>
                    <td>DUS</td>
                    <td>57,000</td>
                    <td>10%</td>
                    <td>456,000</td>
                </tr>
                <tr>
                    <td rowspan="4" colspan="3" style="
">
                        <h2>Keterangan</h2>
                        <ol>
                            <li>Barang yang sudah dibeli tidak bisa ditukar</li>
                            <li>Pembayaran dengan BG/CEK dianggap lunas bila sudah dicairkan</li>
                            <li>Tanda ** menunjukan bonus</li>
                        </ol>

                    </td>
                    <td colspan="4">Subtotal</td>
                    <td>Rp. 1.069.000</td>
                </tr>
                <tr>
                    <td colspan="4">Diskon</td>
                    <td>Rp. 0</td>
                </tr>
                <tr>
                    <td colspan="4">Pajak</td>
                    <td>Rp. 0</td>
                </tr>
                <tr>
                    <td colspan="4">Total</td>
                    <td>Rp. 1.069.000</td>
                </tr>
            </tbody>
        </table>
        <div class="keterangan-tambahan" style="
    text-align: right;">
            <p>* Harga termasuk pajak</p>
        </div>


    </main>




</body>

</html>
