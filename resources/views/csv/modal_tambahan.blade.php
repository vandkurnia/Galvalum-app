No,Tanggal,,Jenis Modal Tambahan,,,Deskripsi,,,,,Jumlah Modal,,
@foreach($modal_tambahan as $m)
{{ $loop->iteration }},{{ $m->tanggal->format('d/m/Y') }},,{{ $m->jenis_modal_tambahan }},,,{{ $m->deskripsi }},,,,,{{ $m->jumlah_modal }},,
@endforeach