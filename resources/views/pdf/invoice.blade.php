<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Modal Tambahan</title>
</head>
<body>
    <h3><center>Laporan Modal Tambahan</center></h3>
    <table style="width: 100%;" border="1" cellspacing="0" cellpadding="5">
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Jenis Modal Tambahan</th>
            <th>Deskripsi</th>
            <th>Jumlah Modal</th>
        </tr>
        @foreach($laporan_modal_tambahan as $s) 
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $s->tanggal }}</td>
            <td>{{ $s->jenis_modal_tambahan}}</td>
            <td>{{ $s->deskripsi }}</td>
            <td>{{ $s->jumlah_modal }}</td>
        </tr>
        @endforeach
    </table>
</body>
</html>