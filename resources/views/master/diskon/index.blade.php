@extends('app')

@section('title', 'Diskon')
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

                <h6 class="m-0 font-weight-bold text-primary">Diskon</h6>
            </div>
            <div class="card-body">
                <div>
                    @if (Auth::user()->role == 'admin')
                        <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#TambahUser"><i
                                class="fa fa-plus"></i> Tambah Diskon</button>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Diskon</th>
                                <th>Nama Diskon</th>
                                <th>Type</th>
                                <th>Besaran</th>
                                <th>Status</th>
                                @if (Auth::user()->role == 'admin')
                                    <th>Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataDiskon as $diskon)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $diskon->kode_diskon }}</td>
                                    <td>{{ $diskon->nama_diskon }}</td>
                                    <td>{{ $diskon->type }}</td>
                                    <td>{{ $diskon->besaran }}</td>
                                    <td>{{ $diskon->status }}</td>
                                    @if (Auth::user()->role == 'admin')
                                        <td>
                                            <button class="btn btn-primary btn-sm"
                                                onclick="funcEditUser('{{ route('diskon.edit', ['id' => $diskon->hash_id_diskon]) }}')"><i
                                                    class="fas fa-edit"></i> Edit</button>
                                            <button class="btn btn-danger btn-sm"
                                                onclick="funcHapusUser('{{ route('diskon.destroy', ['id' => $diskon->hash_id_diskon]) }}', 0)"><i
                                                    class="fas fa-trash"></i> Delete</button>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
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


{{-- Modal Tambah User --}}
<div class="modal fade" id="TambahUser" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah User</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('diskon.store') }}" id="formTambahUser" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="kode_diskon">Kode Diskon:</label>
                        <input type="text" name="kode_diskon" id="kode_diskon" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="nama_diskon">Nama Diskon:</label>
                        <input type="text" name="nama_diskon" id="nama_diskon" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="type">Type:</label>
                        <select name="type" id="type" class="form-control">
                            <option value="percentage">Percentage</option>
                            <option value="amount">Amount</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="besaran">Besaran:</label>
                        <input type="text" name="besaran" id="besaran" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select name="status" id="status" class="form-control">
                            <option value="AKTIF">AKTIF</option>
                            <option value="NONAKTIF">NONAKTIF</option>
                        </select>
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
                <h5 class="modal-title" id="exampleModalLabel">Tambah User</h5>
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
                <h5 class="modal-title" id="exampleModalLabel">Hapus User</h5>
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




@section('javascript-custom')
    <script>
        function validateBesaran(besaran, type) {
            if (type === 'percentage' && (isNaN(besaran) || besaran < 0 || besaran > 100)) {
                return false;
            }
            return true;
        }

        function handleSubmit(formElement) {
            var besaran = formElement.querySelector('#besaran').value;
            var type = formElement.querySelector('#type').value;

            if (!validateBesaran(besaran, type)) {
                // document.getElementById('besaran-warning').style.display = 'block';
                alert('Besaran untuk tipe persentase hanya boleh diisi 0 - 100');
                return false;
            }

            return true;
        }
    </script>


    <script>
        function funcTambahUser() {
            let formtambah = document.querySelector('#formTambahUser');
            if (handleSubmit(formtambah)) {
                formtambah.submit();

            }
            return false;
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
            if (handleSubmit(formedit)) {
                formedit.submit();

            }

            $('#EditUser').modal('hide');
            return false;
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



    <script src="{{ secure_asset('library/datatable/datatables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#bukuBesar').DataTable();
        });
    </script>
@endsection
