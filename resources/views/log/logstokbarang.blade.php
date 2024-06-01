@extends('app')

@section('title', 'Log Stok Barang')
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
                <h6 class="m-0 font-weight-bold text-primary">Log Stok Barang</h6>
            </div>
            <div class="card-body">



                <div class="table-responsive">
                    @foreach ($logStokBarang as $logStkbrng)
                        <div>
                            <strong>
                                <h2>{{ $logStkbrng->admin->nama_admin }} | {{ $logStkbrng->barang->nama_barang }} |
                                    {{ \Carbon\Carbon::parse($logStkbrng->created_at)->translatedFormat('l, d F Y') }} |
                                    @switch($logStkbrng->tipe_log)
                                        @case('create')
                                            <span class="badge badge-primary">Baru</span>
                                        @break

                                        @case('retur_pembeli_create')
                                            <span class="badge badge-success">Retur Pembeli Baru</span>
                                        @break

                                        @case('retur_pembeli_revoke')
                                            <span class="badge badge-warning">Tidak jadi retur</span>
                                        @break

                                        @case('update')
                                            <span class="badge badge-info">Update</span>
                                        @break

                                        @case('pesanan_create')
                                            <span class="badge badge-primary">Pesanan Baru</span>
                                        @break

                                        @case('pesanan_update')
                                            <span class="badge badge-info">Pesanan Update</span>
                                        @break

                                        @case('pesanan_delete')
                                            <span class="badge badge-danger">Pesanan Dihapus</span>
                                        @break

                                        @case('barang_create')
                                            <span class="badge badge-primary">Barang Baru</span>
                                        @break

                                        @case('barang_update')
                                            <span class="badge badge-info">Barang Update</span>
                                        @break

                                        @case('barang_tambah_stok')
                                            <span class="badge badge-success">Barang Tambah Stok</span>
                                        @break

                                        @case('barang_delete')
                                            <span class="badge badge-danger">Barang Dihapus</span>
                                        @break

                                        @case('retur_pembeli_delete')
                                            <span class="badge badge-danger">Retur Pembeli Dihapus</span>
                                        @break

                                        @case('retur_pemasok_create')
                                            <span class="badge badge-success">Retur Pemasok Baru</span>
                                        @break

                                        @case('retur_pemasok_delete')
                                            <span class="badge badge-danger">Retur Pemasok Dihapus</span>
                                        @break

                                        @default
                                            <span class="badge badge-secondary">Unknown</span>
                                    @endswitch
                                </h2>
                            </strong>

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Stok Masuk</th>
                                        <th>Stok Keluar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php

                                        $stok_barang = json_decode($logStkbrng->json_content, true);
                                    @endphp

                                    <tr>
                                        <td>{{ 1 }}</td>
                                        <td>{{ $stok_barang['stok_masuk'] ?? 0 }}</td>
                                        <td>{{ $stok_barang['stok_keluar'] ?? 0 }}</td>
                                    </tr>

                                </tbody>
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
