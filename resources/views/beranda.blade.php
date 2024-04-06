@extends('app')

@section('title', 'Beranda')
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

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-5 mr-5">
                <h2>Barang Pembelian</h2>
                <form action="/proses-input-pembelian" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="nama_barang">Nama Barang:</label>
                        <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
                    </div>
                    <div class="form-group">
                        <label for="jumlah_barang">Jumlah Barang:</label>
                        <input type="number" class="form-control" id="jumlah_barang" name="jumlah_barang" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Barang Pesanan</h6>
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
                                    <a href="#" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="#" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-2">Total Pembayaran: <span id="total_pembayaran"></span></div>
                <button type="submit" class="btn btn-primary mt-4 float-right" data-toggle="modal"
                    data-target="#modalBayar">Bayar</button>
            </div>
        </div>
    </div>
@endsection
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Modal Pembayaran -->
<div class="modal fade" id="modalBayar" tabindex="-1" role="dialog" aria-labelledby="modalBayarLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalBayarLabel">Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Formulir Pembayaran -->
                <form>
                    <div class="form-group">
                        <label for="namaPembeli">Nama Pembeli:</label>
                        <input type="text" class="form-control" id="namaPembeli" placeholder="Masukkan nama pembeli"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="jenisPembelian">Jenis Pembelian:</label>
                        <select class="form-control" id="jenisPembelian" required>
                            <option value="">Pilih jenis pembelian</option>
                            <option value="harga_normal">Harga Normal</option>
                            <option value="reseller">Reseller</option>
                            <option value="potongan">Potongan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat:</label>
                        <input type="text" class="form-control" id="alamat" placeholder="Masukkan alamat"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="noHp">Nomor HP:</label>
                        <input type="text" class="form-control" id="noHp" placeholder="Masukkan nomor HP"
                            required>
                    </div>
                    <div class="form-group" id="jumlahBayarGroup" style="display: none;">
                        <label for="jumlahBayar">Jumlah Bayar:</label>
                        <input type="text" class="form-control" id="jumlahBayar" placeholder="Masukkan jumlah bayar">
                    </div>
                    <div class="form-group">
                        <label for="statusPembayaran">Status Pembayaran:</label>
                        <select class="form-control" id="statusPembayaran" required>
                            <option value="">Pilih status pembayaran</option>
                            <option value="cash">Cash</option>
                            <option value="bon">Bon</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Bayar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
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
    <script>
        // Ambil semua harga barang dari tabel dan hitung totalnya
        var totalHarga = 0;
        $('#dataTable tbody tr').each(function() {
            var harga = parseInt($(this).find('td:nth-child(5)').text().replace('Rp ', '').replace('.', ''));
            totalHarga += harga;
        });

        // Tampilkan total harga dalam elemen span
        $('#total_pembayaran').text('Rp ' + totalHarga);
    </script>

    <script>
        // Definisikan fungsi pencocokan matchStart
        function matchStart(params, data) {
            // Jika tidak ada istilah pencarian, kembalikan semua data
            if ($.trim(params.term) === '') {
                return data;
            }

            // Lewati jika tidak ada properti 'children'
            if (typeof data.children === 'undefined') {
                return null;
            }

            // `data.children` berisi opsi sebenarnya yang kita cocokkan
            var filteredChildren = [];
            $.each(data.children, function(idx, child) {
                if (child.text.toUpperCase().indexOf(params.term.toUpperCase()) === 0) {
                    filteredChildren.push(child);
                }
            });

            // Jika kita cocokkan salah satu dari anak-anak grup zona waktu, atur anak-anak yang cocok di grup
            // dan kembalikan objek grup
            if (filteredChildren.length) {
                var modifiedData = $.extend({}, data, true);
                modifiedData.children = filteredChildren;
                return modifiedData;
            }

            // Kembalikan `null` jika istilah tidak harus ditampilkan
            return null;
        }

        // Inisialisasi Select2 untuk input nama_barang
        $(document).ready(function() {
            $(".js-example-matcher-start").select2({
                matcher: matchStart
            });
        });
    </script>
@endsection
