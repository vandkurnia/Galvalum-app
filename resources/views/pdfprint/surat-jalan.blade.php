<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$title}}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        body{
            /* font-family: Calibri; */
            padding: 10px 10px 10px 10px;
        }

        h2 {
            font-size: 24px;
        }

        header {
            display: flex;
            align-items: end;
            justify-content: space-between;
            margin-bottom:1rem;
        }

        .left-header {
            width: 45%;
        }

        .right-header {
            width: 45%;
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

<body onload="window.print()">
    <!-- Header kosongan -->
    <header>
        <div class="left-header">
            <span style="font-size:16pt;">J A K A R T A</span>
            <div class="informasi-surat-jalan" style="display: flex;align-items: center;gap: 10px;">
                <span style="font-size:20pt;font-weight:bold;padding-top:1rem">SURAT JALAN</span>
                <span style="font-size:16pt;padding-top:1rem">No ..............</span>
            </div>
        </div>

        <div class="right-header">
            <div style="display: flex;flex-direction: row;">
                <span style="font-size:12pt">Jakarta,</span>
                <span class="isian-tanggal" style="border-bottom: 1px dotted;margin-left:5px;width:100%"></span>
            </div>
            <div class="informasi-pelanggan" style="display: flex;flex-direction: column;">
                <span style="font-size:12pt;padding-top:1rem">Kepada Yth:</span>
                <span class="isian-tanggal" style="border-bottom: 1px dotted;padding-top:1rem"> </span>
                <span style="border-bottom: 1px dotted;padding-top:2rem"> </span>
                <span style="border-bottom: 1px solid;padding-top:2rem;margin-left:50%;width:50%"> </span>
                <span style="border-bottom: 1px solid;padding-top:2px;margin-left:50%;width:50%;"> </span>

            </div>
        </div>
    </header>

    <!-- Header menampilkan data dari controller -->
    <!-- <header>
        <div class="left-header">
            <span style="font-size:16pt;">J A K A R T A</span>
            <div class="informasi-surat-jalan" style="display: flex;align-items: center;gap: 10px;">
                <span style="font-size:20pt;font-weight:bold;padding-top:1rem">SURAT JALAN</span>
                @foreach ($dataSuratJalan as $data)
                <span style="font-size:14pt;padding-top:1rem;">No <span style="border-bottom: 1px dotted">{{$data['no_surat']}}</span></span>
                @endforeach
            </div>
        </div>

        <div class="right-header">
            <div style="display: flex;flex-direction: row;">
                <span style="font-size:12pt">Jakarta,</span>
                @foreach ($dataSuratJalan as $data)
                <span class="isian-tanggal" style="border-bottom: 1px dotted;margin-left:5px;width:100%">{{$data['tanggal']}}</span>
                @endforeach
            </div>
            @foreach ($dataPembeli as $index => $data)
            <div class="informasi-pelanggan" style="display: flex;flex-direction: column;">
                <span style="font-size:12pt;padding-top:1rem">Kepada Yth:</span>
                <span class="isian-tanggal" style="border-bottom: 1px dotted;padding-top:0rem">Tn/Ny. {{$data['nama']}}</span>
                <span style="border-bottom: 1px dotted;padding-top:0rem">{{$data['alamat']}} - <span>Telp. </span> {{$data['telp']}}</span>
                <span style="border-bottom: 1px solid;padding-top:2rem;margin-left:50%;width:50%"> </span>
                <span style="border-bottom: 1px solid;padding-top:2px;margin-left:50%;width:50%;"> </span>

            </div>
            @endforeach
        </div>
    </header -->

    <span>
        <p>Kami kirimkan barang - barang dibawah ini dengan kendaraan ...................... No ...................... </p>
    </span>
    <section class="content">
        <table border="1">
            <thead>
                <tr>
                    <th>BANYAKNYA</th>
                    <th>NAMA BARANG</th>
                </tr>
            </thead>

            <tbody>
            @foreach ($dataRincianBarang as $index => $data)
                <tr>
                    <td>{{$data['qty']}}</td>
                    <td>{{$data['nama_barang']}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style=" display: flex;justify-content: space-around;height: 120px;margin-top:2rem">
            <div class="tanda-terima" style="border-bottom: 1px dotted;">
                <h2>Tanda terima</h2>
            </div>
            <div class="hormat-kami" style="border-bottom: 1px dotted;">
                <h2>Hormat Kami</h2>
            </div>
        </div>
    </section>





</body>

</html>
