@extends('app')

@section('title', 'Log Nota Pembelian')
@section('header-custom')
    <link href="{{ secure_asset('library/datatable/datatables.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Log Nota Pembelian</h6>
            </div>
            <div class="card-body">



                <div class="table-responsive">
                    @foreach ($logNotaData as $logNota)
                    <div style="margin-bottom: 90px; border-top: 2px solid gray; padding-top: 20px;">
                            <strong>
                                <h2 >{{ $logNota->admin->nama_admin }} | {{ $logNota->nota->no_nota }} |
                                    {{ \Carbon\Carbon::parse($logNota->created_at)->translatedFormat('l, d F Y') }} | @switch($logNota->tipe_log)
                                    @case('create')
                                        <span class="badge badge-primary">Baru</span>
                                        @break
                    
                                    @case('retur_pembeli_create')
                                        <span class="badge badge-success">Retur</span>
                                        @break
                    
                                    @case('retur_pembeli_revoke')
                                        <span class="badge badge-warning">Tidak jadi retur</span>
                                        @break
                    
                                    @case('update')
                                        <span class="badge badge-info">Update</span>
                                        @break
                    
                                    @default
                                        <span class="badge badge-secondary">Unknown</span>
                                @endswitch</h2>
                            </strong>

                            <table class="table">
                                <tr>
                                    <th>ID Nota</th>
                                    <td>{{ $logNota->json_content['id_nota'] }}</td>
                                </tr>
                                <tr>
                                    <th>No Nota</th>
                                    <td>{{ $logNota->json_content['no_nota'] }}</td>
                                </tr>
                                <tr>
                                    <th>ID Pembeli</th>
                                    <td>{{ $logNota->json_content['id_pembeli'] }}</td>
                                </tr>
                                <tr>
                                    <th>ID Admin</th>
                                    <td>{{ $logNota->json_content['id_admin'] }}</td>
                                </tr>
                                <tr>
                                    <th>Metode Pembayaran</th>
                                    <td>{{ $logNota->json_content['metode_pembayaran'] }}</td>
                                </tr>
                                <tr>
                                    <th>Sub Total</th>
                                    <td>{{ $logNota->json_content['sub_total'] }}</td>
                                </tr>
                                <tr>
                                    <th>Nominal Terbayar</th>
                                    <td>{{ $logNota->json_content['nominal_terbayar'] }}</td>
                                </tr>
                                <tr>
                                    <th>Tenggat Bayar</th>
                                    <td>{{ $logNota->json_content['tenggat_bayar'] }}</td>
                                </tr>
                                <tr>
                                    <th>Diskon</th>
                                    <td>{{ $logNota->json_content['diskon'] }}</td>
                                </tr>
                                <tr>
                                    <th>Ongkir</th>
                                    <td>{{ $logNota->json_content['ongkir'] }}</td>
                                </tr>
                                <tr>
                                    <th>Total</th>
                                    <td>{{ $logNota->json_content['total'] }}</td>
                                </tr>
                            </table>
                            <table class="table">

                                <tr>
                                    <th>Nama Barang</th>
                                    <th>Jumlah Pembelian</th>
                                    <th>Harga</th>
                                    <th>Jenis Pembelian</th>
                                    <th>Harga Potongan</th>
                                </tr>
                                @foreach ($logNota->json_content['pesanan_pembeli'] as $pesanan_pembeli)
                                    <tr>
                                        <td>@php
                                            $barangs = DB::table('barangs')->where('id_barang', $pesanan_pembeli['id_barang'])->first();
                                            echo $barangs->nama_barang;
                                        @endphp</td>
                                       <td>{{ number_format($pesanan_pembeli['jumlah_pembelian'] ?? 0, 1) }}</td>
                                        <td>{{ 'Rp ' . number_format($pesanan_pembeli['harga'], 0, ',', '.')  }}</td>
                                        <td> @switch($pesanan_pembeli['jenis_pembelian'])
                                            @case('aplicator')
                                                Aplicator
                                                @break
                                            @case('potongan')
                                                Potongan
                                                @break
                                            @default
                                                Harga Normal
                                        @endswitch </td>
                                        <td>{{ $pesanan_pembeli['harga_potongan'] }}</td>
                                    </tr>
                                @endforeach

                            </table>
                        </div>
                    @endforeach

                </div>



            </div>
        </div>
    </div>
@endsection

@section('javascript-custom')
    <script src="{{ secure_asset('library/datatable/datatables.min.js') }}"></script>
    {{-- <script>
        $(document).ready(function() {
            $('#lognota').DataTable();
        });
    </script> --}}
@endsection
