No,Tanggal,,Nama Pengeluaran,,,Deskripsi,,,,,Jumlah Pengeluaran,,
@foreach($kas_keluar as $kk)
{{ $loop->iteration }},{{ $kk->tanggal->format('d/m/Y') }},,{{ $kk->nama_pengeluaran }},,,{{ $kk->deskripsi }},,,,,{{ $kk->jumlah_pengeluaran }},,
@endforeach