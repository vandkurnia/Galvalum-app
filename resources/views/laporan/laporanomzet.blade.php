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

                <h6 class="m-0 font-weight-bold text-primary">Laporan Omzet</h6>
                <!-- Filter Tanggal -->
                <div class="form-group">
                    <label for="tanggal">Filter Tanggal:</label>
                    <input type="date" id="tanggal" name="tanggal" class="form-control">
                </div>

            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th>Jumlah Pembelian</th>
                                <th>Jenis Pembelian</th>
                                <th>Omzet</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total = 0;

                                $typePembelian = [
                                    'aplicator' => 'Aplicator',
                                    'harga_normal' => 'Harga Normal',
                                    'potongan' => 'Potongan',
                                ];
                            @endphp
                            @foreach ($dataNotaPembelian as $notaPembelian)
                                <tr>
                                    <td>{{ $notaPembelian['nama_barang'] }}</td>
                                    <td>{{ $notaPembelian['jumlah_pembelian'] }}</td>
                                    <td>{{ $typePembelian[$notaPembelian['jenis_pembelian']] }}</td>
                                    <td>{{ 'Rp ' . number_format($notaPembelian['omzet'], 0, ',', '.') }} </td>
                                </tr>
                                @php
                                    $total += $notaPembelian['omzet'];
                                @endphp
                            @endforeach


                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3">Total</td>
                                <td> {{ 'Rp ' . number_format($total, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
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
