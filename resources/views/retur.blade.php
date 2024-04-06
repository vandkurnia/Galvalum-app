@extends('app')
@section('title', 'Retur')
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
    <div class="container">
        <div class="row">
            <div class="col-md-5 mr-5">
                <h2>Retur</h2>
                <form action="/proses-input-pembelian" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="nama_barang">Id Pembelian:</label>
                        <input type="text" class="form-control" id="id_pembelian" name="id_pembelian" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Pembelian</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Tipe Barang</th>
                                <th>Ukuran Barang</th>
                                <th>Harga Barang</th>
                                <th>Jumlah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th>1</th>
                                <td>Galvalum Sheet 0.3mm</td>
                                <td>Sheet</td>
                                <td>0.3mm</td>
                                <td>150000</td>
                                <td>100</td>
                                <td>
                                    <a>
                                        <button type="submit" class="btn btn-primary btn-sm" data-toggle="modal"
                                            data-target="#modalRetur">
                                            <span><i class="fas fa-edit"></i></span>Edit</button>
                                    </a>
                                    <a href="#" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


<!-- Modal Retur -->
<div class="modal fade" id="modalRetur" tabindex="-1" role="dialog" aria-labelledby="modalReturLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalReturLabel">Retur</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Formulir Pembayaran -->
                <form>
                    <div class="form-group">
                        <label for="tanggalRetur">Tanggal Retur:</label>
                        <input type="date" class="form-control" id="tanggalRetur" placeholder="" required>
                    </div>
                    <div class="form-group">
                        <label for="jenisPembelian">Jenis Retur:</label>
                        <select class="form-control" id="jenisRetur" required>
                            <option value="">Pilih jenis retur</option>
                            <option value="barang_rusak">Barang Rusak</option>
                            <option value="tidak_rusak">Tidak Rusak</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="buktiRetur">Bukti Retur:</label>
                        <input type="file" class="form-control-file" id="buktiRetur" accept="image/*" required>
                    </div>
                    <div class="form-group">
                        <label for="noHp">Keterangan:</label>
                        <input type="text" class="form-control" id="Keterangan" placeholder="Keterangan"
                            required>
                    </div>
                </form>
                <button type="submit" class="btn btn-primary">Retur</button>
            </div>
        </div>
    </div>
</div>

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <a class="btn btn-primary" href="login.html">Logout</a>
            </div>
        </div>
    </div>
</div>


@section('javascript-custom')
@endsection