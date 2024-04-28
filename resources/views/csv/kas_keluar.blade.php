No,Tanggal,,Deskripsi,,,,,Jumlah Pengeluaran,,
@foreach($kas_keluar as $kk)
{{ $loop->iteration }},{{ $kk->tanggal->format('d/m/Y') }},,{{ $kk->keterangan }},,,,,{{ $kk->kredit }},,
@endforeach