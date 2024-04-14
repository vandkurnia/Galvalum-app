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
                    {{-- <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
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
                    </table> --}}
                    <table class="table table-bordered" id="dataTransaksi" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Pembeli</th>
                                <th>Jenis Pembelian</th>
                                <th>Metode Pembelian</th>
                                <th>Tanggal Pembayaran</th>
                                <th>Total Pembelian</th>
                                <th>Nama Penginput</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                            @php
                                $no = 0;
                            @endphp
                            @foreach ($dataNotaPembeli as $notaPembeli)
                                <tr>
                                    <td>{{ ++$no }}</td>
                                    <td>{{ $notaPembeli->Pembeli->nama_pembeli }}</td>
                                    <td>{{ $notaPembeli->jenis_pembelian }}</td>
                                    <td>{{ $notaPembeli->status_pembelian }}</td>
                                    <td>{{ $notaPembeli->created_at }}</td>
                                    <td>{{ 10000 }}</td>
                                    <td>{{ $notaPembeli->Admin->nama_admin }}</td>
                                    <td>
                                        <a href="{{ route('pemesanan.edit', ['id' => $notaPembeli->id_nota]) }}"
                                            class="btn btn-primary btn-sm"><i class="fas fa-edit"></i>
                                            Edit</a>
                                        <button class="btn btn-danger btn-sm"
                                            onclick="funcHapusUser('{{ route('pemesanan.destroy', ['id' => $notaPembeli->id_nota]) }}', 0)"><i
                                                class="fas fa-trash"></i>
                                            Delete</button>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


{{-- Modal Delete --}}
<div class="modal fade" id="HapusUser" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Hapus Tipe Barang</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formHapusUser" method="POST">
                    @csrf
                    @method('DELETE')
                    <h1>Apakah anda yakin ingin menghapus ?</h1>


                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="funcHapusUser(null, 1)">Simpan</button>
            </div>
        </div>
    </div>
</div>
{{-- End of Modal Delete --}}


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
        function funcTambahUser() {
            let formtambah = document.querySelector('#formTambahUser');
            formtambah.submit();
        }

        function funcEditUser(url) {
            var url = url;

            // Kirim request Ajax
            fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(function(response) {
                // Handle response
                console.log(response.code);

                if (response.ok) {
                    response.json().then(function(data) {
                        $('#EditUser .modal-body').html(data
                            .data); // Menetapkan respons ke elemen HTML dengan ID editUser
                        $('#EditUser').modal('show');
                    });
                } else {
                    alert('Response was not ok');
                }
                // Memuat modal EditUser dengan data pengguna
                // $('#editUserModal').html(response);

            }).catch(function(error) {
                // Handle error
                alert('Terjadi kesalahan');
            });

        }

        function funcUpdateUser() {
            let formedit = $('#EditUser .modal-body #formEditUser');
            formedit.submit();
            $('#EditUser').modal('hide');
        }


        function funcHapusUser(url, typeoperasi) {
            // 0 = Menampilkan modal, 1 = Submit penghapusan
            if (typeof(typeoperasi) === "number") {
                if (typeoperasi === 1) {
                    let elementFormHapus = document.querySelector('#HapusUser #formHapusUser');
                    elementFormHapus.submit();

                } else {
                    // Menampilkan modal delete
                    $('#HapusUser').modal('show');

                    // Mengatur nilai action formulir hapus user sesuai dengan hashIdAdmin
                    $('#formHapusUser').attr('action', url);
                }
            } else {
                console.error(typeoperasi);
                alert('Kesalahan pada parameter typeoperasi');

            }


        }
    </script>


@endsection
