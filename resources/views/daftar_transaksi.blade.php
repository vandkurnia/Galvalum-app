@extends('app')

@section('title', 'Daftar Transaksi')

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
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Data Transaksi</h6>
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
