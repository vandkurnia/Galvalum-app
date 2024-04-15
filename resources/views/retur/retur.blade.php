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
                <h6 class="m-0 font-weight-bold text-primary">Daftar Retur</h6>
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
                                <th scope="col">No</th>
                                <th scope="col">Tanggal Retur</th>
                                <th scope="col">Bukti</th>
                                <th scope="col">Jenis Retur</th>
                                <th scope="col">Keterangan</th>
                                <th scope="col">Aksi</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataRetur as $retur)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $retur->tanggal_retur }}</td>
                                    <td>{{ $retur->bukti }}</td>
                                    <td>{{ $retur->jenis_retur }}</td>
                                    <td>{{ $retur->keterangan }}</td>
                                    <td>
                                        <a class="btn btn-primary btn-sm"
                                            href="{{ route('retur.edit', ['id_retur' => $retur->id_retur]) }}"><i
                                                class="fas fa-edit"></i>
                                            Edit</a>

                                        <button class="btn btn-danger btn-sm"
                                            onclick="funcHapusUser('{{ route('retur.destroy', ['id_retur' => $retur->id_retur]) }}', 0)"><i
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


<!-- Modal Retur -->
{{-- <div class="modal fade" id="modalRetur" tabindex="-1" role="dialog" aria-labelledby="modalReturLabel"
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
                        <input type="text" class="form-control" id="Keterangan" placeholder="Keterangan" required>
                    </div>
                </form>
                <button type="submit" class="btn btn-primary">Retur</button>
            </div>
        </div>
    </div>
</div> --}}

{{-- Modal Delete --}}
<div class="modal fade" id="HapusUser" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Hapus Barang</h5>
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
