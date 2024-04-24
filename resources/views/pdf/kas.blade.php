<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Kas Keluar</title>
</head>
<body>
    <h3><center>Laporan Kas Keluar</center></h3>
    <table style="width: 100%;" border="1" cellspacing="0" cellpadding="5">
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Nama Pengeluaran</th>
            <th>Deskripsi</th>
            <th>Jumlah Pengeluaran</th>
        </tr>
        @foreach($laporan_kas_keluar as $s) 
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $s->tanggal }}</td>
            <td>{{ $s->nama_pengeluaran }}</td>
            <td>{{ $s->deskripsi }}</td>
            <td>{{ $s->jumlah_pengeluaran }}</td>
        </tr>
        @endforeach
    </table>
</body>
</html>