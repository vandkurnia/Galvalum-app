No,Tanggal,,Deskripsi,,,,,Jumlah Modal,,
@foreach($modal_tambahan as $m)
{{ $loop->iteration }},{{ $m->tanggal->format('d/m/Y') }},,{{ $m->keterangan }},,,,,{{ $m->debit }},,
@endforeach