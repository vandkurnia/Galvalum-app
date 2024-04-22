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
    <div class="container-fluid">

        <div class="row">
            <div class="col-md-5 mr-5">
                <h2>Retur</h2>
                {{-- <form action="/proses-input-pembelian" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="nama_barang">Id Pembelian:</label>
                        <input type="text" class="form-control" id="id_pembelian" name="id_pembelian" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form> --}}
            </div>
        </div>
    </div>

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
                <h6 class="m-0 font-weight-bold text-primary">Daftar Retur Pemasok</h6>
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
                                    <a>
                                        <button type="submit" class="btn btn-primary btn-sm" data-toggle="modal"
                                            data-target="#modalRetur">
                                            <span><i class="fas fa-edit"></i></span>Edit</button>
                                    </a>
                                    <a href="#" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                        </tbody>
                    </table> --}}
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No. Retur</th>
                                <th>Faktur Retur</th>
                                <th>Tanggal Retur</th>
                                <th>Bukti Retur</th>
                                <th>Jenis Retur</th>
                                <th>Total Nilai Retur</th>
                                <th>Pengembalian Data</th>
                                <th>Kekurangan</th>
                                <th>Status</th>
                                <th>Nama Pemasok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataReturPemasok as $retur)
                                <tr>
                                    <td>{{ $retur->no_retur_pemasok }}</td>
                                    <td>{{ $retur->faktur_retur_pemasok }}</td>
                                    <td>{{ $retur->tanggal_retur }}</td>
                                    <td>{{ $retur->bukti_retur_pemasok }}</td>
                                    <td>{{ $retur->jenis_retur }}</td>
                                    <td>{{ $retur->total_nilai_retur }}</td>
                                    <td>{{ $retur->pengembalian_data }}</td>
                                    <td>{{ $retur->kekurangan }}</td>
                                    <td>{{ $retur->status }}</td>
                                    <td>{{ $retur->pemasok->nama_pemasok }}</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm"
                                            onclick="funcEditUser('{{ route('retur.pemasok.edit', ['id_retur' => $retur->hash_id_retur_pemasok]) }}')"><i
                                                class="fas fa-edit"></i>
                                            Edit</button>
                                        <button class="btn btn-danger btn-sm"
                                            onclick="funcHapusUser('{{ route('retur.pemasok.destroy', ['id_retur' => $retur->hash_id_retur_pemasok]) }}', 0)"><i
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
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Retur Pembeli</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No. Retur</th>
                                <th>Faktur Retur</th>
                                <th>Tanggal Retur</th>
                                <th>Bukti Retur</th>
                                <th>Jenis Retur</th>
                                <th>Total Nilai Retur</th>
                                <th>Pengembalian Data</th>
                                <th>Kekurangan</th>
                                <th>Status</th>
                                <th>Nama Pembeli</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataReturPembeli as $retur)
                                <tr>
                                    <td>{{ $retur->no_retur_pembeli }}</td>
                                    <td>{{ $retur->faktur_retur_pembeli }}</td>
                                    <td>{{ $retur->tanggal_retur_pembeli }}</td>
                                    <td>{{ $retur->bukti_retur_pembeli }}</td>
                                    <td>{{ $retur->jenis_retur }}</td>
                                    <td>{{ $retur->total_nilai_retur }}</td>
                                    <td>{{ $retur->pengembalian_data }}</td>
                                    <td>{{ $retur->kekurangan }}</td>
                                    <td>{{ $retur->status }}</td>
                                    <td>{{ $retur->pembeli->nama_pembeli }}</td>
                                    <td>

                                        <a class="btn btn-primary btn-sm"
                                            href="{{ route('retur.pembeli.edit', ['id_retur' => $retur->hash_id_retur_pembeli]) }}"><i class="fas fa-edit"></i>Edit</a>

                             
                                        <button class="btn btn-danger btn-sm"
                                            onclick="funcHapusUser('{{ route('retur.pembeli.destroy', ['id_retur' => $retur->hash_id_retur_pembeli]) }}', 0)"><i
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


{{-- Modal Tambah User --}}
<div class="modal fade" id="TambahUser" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Tipe Barang</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('pembeli.store') }}" id="formTambahUser" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="nama_pembeli" class="form-label">Nama Pembeli</label>
                        <input type="text" class="form-control" id="nama_pembeli_store" name="nama_pembeli" required>
                    </div>
                    <div class="form-group">
                        <label for="alamat_pembeli" class="form-label">Alamat Pembeli</label>
                        <input type="text" class="form-control" id="alamat_pembeli_store" name="alamat_pembeli"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="no_hp_pembeli" class="form-label">No HP Pembeli</label>
                        <input type="text" class="form-control" id="no_hp_pembeli_store" name="no_hp_pembeli"
                            required>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="funcTambahUser()">Simpan</button>
            </div>
        </div>
    </div>
</div>
{{-- End of Modal Tambah User --}}

{{-- Modal Edit --}}
<div class="modal fade" id="EditUser" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Tipe Barang</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="funcUpdateUser()">Simpan</button>
            </div>
        </div>
    </div>
</div>
{{-- End of Modal Edit --}}

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
