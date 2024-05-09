<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    {{-- <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet"> --}}
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
        integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w=="
        crossorigin="anonymous" referrerpolicy="no-referrer" /> --}}
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous"> --}}
    <style>
        body {
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
            margin-bottom: 1rem;
        }

        h1 {
            display: flex;
        }

        /* CSS hanya berlaku untuk elemen dengan kelas .table-style */
        .table-style table {
            border-collapse: collapse;
            width: 100%;
        }

        .table-style table,
        .table-style th,
        .table-style td {
            border: 1px solid black;
        }

        .table-style th,
        .table-style td {
            padding: 8px;
            text-align: left;
        }

        .table-style th {
            background-color: #f2f2f2;
        }

        .table-style tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body onload="window.print()">
    <!-- Header kosongan -->
    <header style="position: relative;">
        <div class="left-header"
            style="position:absolute; left:0; width: 300px;height: 140px;">
            <div style="position: relative;">

                <span style="font-size:16pt;  margin-top: 20px;">M A D I U N</span>
                <div class="informasi-surat-jalan" style="align-items: center;gap: 10px;">
                    <span style="font-size:20pt;font-weight:bold;padding-top:1rem">SURAT JALAN<br></span> 
                    <span style="font-size:16pt;padding-top:1rem">No {{ $dataSuratJalan[0]['no_surat'] }}</span>
                </div>
            </div>

        </div>
        <div class="right-header"
            style="position:absolute; right:0; width: 300px;height: 140px;">
            <div style="display: flex;flex-direction: row;">
                <span style="font-size:12pt">Madiun,</span>
                @php
                    setlocale(LC_TIME, 'id_ID');
                    $tanggal = new DateTime($dataSuratJalan[0]['tanggal']);
                    $formatTanggal = $tanggal->format('d M Y');
                @endphp
                <span class="isian-tanggal"
                    style="border-bottom: 1px dotted; margin-left: 5px; width: 100%">{{ $formatTanggal }}<br><br></span>
            </div>
            <div class="informasi-pelanggan" style="display: flex;flex-direction: column;">
                <span style="font-size:12pt;padding-top:1rem">Kepada Yth:<br><br></span>
                {{-- <span class="isian-tanggal" style="border-bottom: 1px dotted;padding-top:1rem"></span>    --}}
                <span style="border-bottom: 1px dotted;padding-top:5px">{{ $dataPembeli[0]['nama'] }}<br></span>
                <span style="border-bottom: 1px dotted;padding-top:5px">{{ $dataPembeli[0]['alamat'] }}<br></span>
                <span style="border-bottom: 1px dotted;padding-top:5px">{{ $dataPembeli[0]['telp'] }}<br></span>
                <span style="border-bottom: 1px solid;padding-top:2rem;margin-left:50%;width:50%"></span>
                <span style="border-bottom: 1px solid;padding-top:2px;margin-left:50%;width:50%;"></span>

            </div>
        </div>



    </header>



    <span style="position: relative; top: 150px;">
        <p>Kami kirimkan barang - barang dibawah ini dengan kendaraan ...................... No ......................
        </p>
    </span>
    <section style="position: relative;top: 150px;" class="content table-style">
        <table>
            <thead>
                <tr>
                    <th>BANYAKNYA</th>
                    <th>NAMA BARANG</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($dataRincianBarang as $index => $data)
                    <tr>
                        <td>{{ (int) $data['qty']}}</td>
                        <td>{{ $data['nama_barang'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="position: relative;height: 120px;margin-top:2rem; text-align: center;">
            <div class="tanda-terima" style="position: absolute; left: 0; padding-left: 40px;">
                <h2 style="font-size: 18px;">Tanda Terima</h2>
                <p style="padding-top: 40px;">.....................................</p>
            </div>
            <div class="hormat-kami"  style="position: absolute; right: 0;padding-right: 40px;">
                <h2  style="font-size: 18px;">Hormat Kami</h2>
                <p style="padding-top: 40px;">.....................................</p>
            </div>
        </div>
    </section>





</body>

</html>
