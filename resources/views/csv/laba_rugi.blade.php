,,,REKAP RINCIAN PENJUALAN                          
,,,{{ $hari }} {{ $tanggal }}                       
PENJUALAN KOTOR,,,,,,,Rp {{ number_format($total_penjualan_kotor, 0, ',', '.') }}
@foreach($modal as $m)
MODAL,,,,,,,Rp {{ number_format($m->debit, 0, ',', '.') }} (+)
@endforeach
,,,,,,,Rp {{ number_format($total1, 0, ',', '.') }}
TAMBAHAN MODAL,,,,,,,
@foreach($modal_tambahan as $th)
(+) {{ $th->keterangan }},,,,,Rp {{ number_format($th->debit, 0, ',', '.') }} (+)
@endforeach
JUMLAH TAMBAHAN MODAL,,,,,,,Rp {{ number_format($total_modal_tambahan, 0, ',', '.') }} (+)
LABA KOTOR,,,,,,,Rp {{ number_format($laba_kotor, 0, ',', '.') }}
PENGURANGAN/PENGELUARAN,,,,,,,
@foreach($keluar as $p)
(-) {{$p->keterangan}},,,,,Rp {{ number_format($p->kredit, 0, ',', '.') }} (+)
@endforeach
JUMLAH PENGURANGAN/PENGELUARAN,,,,,,,Rp {{ number_format($total_keluar, 0, ',', '.') }} (-)
LABA BERSIH,,,,,,,Rp {{ number_format($laba_bersih, 0, ',', '.') }}
@foreach($modal_darurat as $md)
(-) MODAL HARI INI,,,,,,,Rp {{ number_format($md->kredit, 0, ',', '.') }} (-)
@endforeach
TOTAL TRANSFER/SETOR TUNAI,,,,,,,Rp {{ number_format($total_transfer, 0, ',', '.') }}
