,,,REKAP RINCIAN PENJUALAN                          
,,,{{ $hari }} {{ $tanggal }}                       
@foreach($penjualan_kotor as $pk)
PENJUALAN KOTOR,,,,,,,Rp {{ number_format($pk->debit, 0, ',', '.') }}
@endforeach
@foreach($modal as $m)
MODAL,,,,,,,Rp {{ number_format($m->debit, 0, ',', '.') }} (+)
@endforeach
,,,,,,,Rp {{ number_format($total1, 0, ',', '.') }}
TAMBAHAN MODAL,,,,,,,
@foreach($tambahan_modal as $th)
(+) {{ $th->jenis_modal_tambahan }},,,,,Rp {{ number_format($th->jumlah_modal, 0, ',', '.') }} (+)
@endforeach
JUMLAH TAMBAHAN MODAL,,,,,,,Rp {{ number_format($jumlah_tambahan_modal, 0, ',', '.') }} (+)
LABA KOTOR,,,,,,,Rp {{ number_format($laba_kotor, 0, ',', '.') }}
PENGURANGAN/PENGELUARAN,,,,,,,
@foreach($pengeluaran as $p)
(-) {{$p->nama_pengeluaran}},,,,,Rp {{ number_format($p->jumlah_pengeluaran, 0, ',', '.') }} (+)
@endforeach
JUMLAH PENGURANGAN/PENGELUARAN,,,,,,,Rp {{ number_format($total_pengeluaran, 0, ',', '.') }} (-)
LABA BERSIH,,,,,,,Rp {{ number_format($laba_bersih, 0, ',', '.') }}
@foreach($modal_darurat as $md)
(-) MODAL HARI INI,,,,,,,Rp {{ number_format($md->kredit, 0, ',', '.') }} (-)
@endforeach
TOTAL TRANSFER/SETOR TUNAI,,,,,,,Rp {{ number_format($total_transfer, 0, ',', '.') }}
