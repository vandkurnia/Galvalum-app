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
                        <div>
                            <strong>
                                <h2>{{ $logNota->admin->nama_admin }} | {{ $logNota->nota->no_nota }} |
                                    {{ \Carbon\Carbon::parse($logNota->created_at)->translatedFormat('l, d F Y') }}</h2>
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
                                    <th>Jumlah Pembelian</th>
                                    <th>Harga</th>
                                    <th>Jenis Pembelian</th>
                                    <th>Harga Potongan</th>
                                </tr>
                                @foreach ($logNota->json_content['pesanan_pembeli'] as $pesanan_pembeli)
                                    <tr>
                                        <td>{{ (int) $pesanan_pembeli['jumlah_pembelian'] }}</td>
                                        <td>{{ $pesanan_pembeli['harga'] }}</td>
                                        <td>{{ $pesanan_pembeli['jenis_pembelian'] }}</td>
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
