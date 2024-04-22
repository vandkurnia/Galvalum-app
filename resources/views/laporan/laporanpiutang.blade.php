@extends('app')

@section('title', 'User')
@section('header-custom')
    <style>
        /* Sembunyikan Showing entries */
        #dataTable_length,
        #dataTable_info {
            display: none;
        }

        /* Sembunyikan pagination */
        #dataTable_paginate {
            display: none;
        }
    </style>
    

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

        <!-- error -->
        @if ($errors->any())
            @foreach ($errors->all() as $err)
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-exclamation-circle" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                        <path
                            d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z" />
                    </svg>
                    <strong>Error!</strong> {{ $err }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endforeach
        @endif
        <div class="card shadow mb-4">
            <div class="card-header py-3">

                <h6 class="m-0 font-weight-bold text-primary">Laporan Piutang</h6>
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Pembeli</th>
                                <th>Handphone</th>
                                <th>Barang Pembelian</th>
                                <th>Jumlah Pembelian</th>
                                <th>Jenis Pelanggan</th>
                                <th>Tanggal Pembelian</th>
                                <th>Harga Total</th>
                                <th>Jumlah Terbayar</th>
                                <th>Kekurangan</th>
                                <th>Jatuh Tempo</th>
                                <th>Status</th>
                                <th>Lunas</th>
                                <th>Update</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Pembeli A</td>
                                <td>08123456789</td>
                                <td>Barang A</td>
                                <td>10</td>
                                <td>Pelanggan A</td>
                                <td>2024-05-01</td>
                                <td>$100</td>
                                <td>$50</td>
                                <td>$50</td>
                                <td>2024-06-01</td>
                                <td><span class="badge badge-warning">Belum Dibayar</span></td>
                                <td>Hutang</td>
                                <td><button class="btn btn-primary">Update</button></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Pembeli B</td>
                                <td>08123456788</td>
                                <td>Barang B</td>
                                <td>20</td>
                                <td>Pelanggan B</td>
                                <td>2024-05-05</td>
                                <td>$200</td>
                                <td>$200</td>
                                <td>$0</td>
                                <td>2024-06-05</td>
                                <td><span class="badge badge-success">Lunas</span></td>
                                <td>Dibayar</td>
                                <td><button class="btn btn-primary">Update</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

<!-- End of Page Wrapper -->
<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>





@section('javascript-custom')

@endsection
