<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan</title>
    <style>
        p {
            font-size: 26px;
        }

        h2 {
            font-size: 24px;
        }

        header {
            display: flex;
            border: 1px solid blue;
        }

        .left-header {
            flex-grow: 1;
        }

        .right-header {
            flex-grow: 1;
        }

        h1 {
            display: flex;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <header>
        <div class="left-header">
            <h2>{{ $notaPembelian->Pembeli->alamat_pembeli }}</h2>

            <div class="informasi-surat-jalan" style="
    display: flex;
    align-items: center;
    gap: 10px;
">
                <h1>Surat jalan</h1>
                <p>No ..............</p>
            </div>
        </div>
        <div class="right-header">
            <p style="
    display: flex;
">{{ $notaPembelian->Pembeli->alamat_pembeli }} ,<span class="isian-tanggal"
                    style="
    border-bottom: 1px dotted;
    flex-grow: 1;
">{{ date('Y-m-d', strtotime($notaPembelian->Pembeli->created_at)) }}</span> </p>
            <div class="informasi-pelanggan" style="
    display: flex;
    flex-direction: column;
">
                <p>Kepada Yth:</p>
                <p class="isian-pelanggan"
                    style="
    height: 10px;
    text-decoration-line: underline;
    text-decoration-style: dotted;
">
                    {{ $notaPembelian->Pembeli->nama_pembeli }}</p>
                <p>.....................................................</p>

            </div>
        </div>
    </header>
    <span>
        <p>Kami kirimkan barang - barang dibawah ini dengan kendaraan ......... No ........</p>
    </span>
    <section class="content">
        <table border="1">
            <thead>
                <tr>
                    <th>Banyaknya</th>
                    <th>Nama Barang</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($dataPesanan as $pesanan)
                <tr>

                    <td>{{ (int) $pesanan->jumlah_pembelian }}</td>
                    <td>{{ $pesanan->Barang->nama_barang }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <span style="
    display: flex;
    justify-content: space-around;
    height: 200px;
">
            <div class="tanda-terima" style="
    border-bottom: 1px dotted;
    margin-bottom: 30px;
">
                <h2>Tanda terima</h2>
            </div>
            <div class="hormat-kami" style="border-bottom: 1px dotted;
    margin-bottom: 30px;
">
                <h2>Hormat Kami</h2>
            </div>
        </span>
    </section>





</body>

</html>
