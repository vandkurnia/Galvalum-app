@extends('app')

@section('title', 'User')
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

                <h6 class="m-0 font-weight-bold text-primary">Laporan Modal Tambahan</h6>
                <form>
                    <div class="form-group">
                        <label for="tanggal">Filter Tanggal:</label>
                        <input type="date" id="tanggal" name="tanggal" class="form-control">
                    </div>
                    <div class="form-group">
                        <button onclick="updateTanggal()" class="btn btn-success">Filter</button>
                        <a class="btn btn-info" href="{{ url('laporan/modal-tambahan') }}">Refresh</a>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#TambahModalTambahan"><i
                            class="fa fa-plus"></i> Tambah Modal Tambahan</button>
                    <div class="btn-group btn-group-toggle float-right" data-toggle="buttons">
                        <a class="btn btn-danger float-right" href="{{ url('laporan/generate-pdf') }}"><i
                                class="fas fa-file-pdf" aria-hidden="true"></i> PDF</a>
                        <a class="btn btn-success float-right" href="{{ url('laporan/generate-csv') }}"><i
                                class="fas fa-file-csv" aria-hidden="true"></i> CSV</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" id="modalTambahan" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Deskripsi</th>
                                <th>Jumlah Modal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($laporan_modal_tambahan as $lpmdl)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $lpmdl->tanggal }}</td>
                                    <td>{{ $lpmdl->keterangan }}</td>
                                    <td>{{ $lpmdl->debit }}</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm"
                                            onclick="funcEditUser('{{ route('laporan.editModal', ['id' => $lpmdl->id_bukubesar]) }}')"><i
                                                class="fas fa-edit"></i>
                                            Edit</button>
                                        <button class="btn btn-danger btn-sm"
                                            onclick="funcHapusUser('{{ route('laporan.modaldestroy', ['id' => $lpmdl->id_bukubesar]) }}', 0)"><i
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

<!-- End of Page Wrapper -->
<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>
{{-- Modal Tambah Modal Tambahan --}}
<div class="modal fade" id="TambahModalTambahan" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Modal Tambahan</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('laporan.simpanModal') }}" id="formTambahUser" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="id_akunbayar">Akun Bayar</label>
                        <select class="form-control" id="id_akunbayar" name="id_akunbayar">
                            @foreach ($dataAkunBayar as $akunBayar)
                                <option value="{{ $akunBayar->hash_id_akunbayar }}">{{ $akunBayar->nama_akun }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nama_tipe" class="form-label">Deskripsi</label>
                        <input type="text" class="form-control" id="keterangan" name="keterangan" required>
                    </div>
                    <div class="form-group">
                        <label for="nama_tipe" class="form-label">Jumlah Modal</label>
                        <input type="text" class="form-control" id="debit" name="debit" required>
                    </div>
                    <div class="form-group">
                        <label for="nama_tipe" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" required>
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
{{-- End of Modal Tambah Modal Tambahan  --}}

{{-- Modal Edit --}}
<div class="modal fade" id="EditUser" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Modal Tambahan</h5>
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
                <h5 class="modal-title" id="exampleModalLabel">Hapus Modal Tambahan</h5>
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
    <script src="{{ secure_asset('library/datatable/datatables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#modalTambahan').DataTable();
        });
    </script>
    <script>
        // Fungsi untuk mendapatkan hari dalam bahasa Indonesia
        function getHariIndonesia(day) {
            const hari = ["MINGGU", "SENIN", "SELASA", "RABU", "KAMIS", "JUMAT", "SABTU"];
            return hari[day];
        }

        // Fungsi untuk mendapatkan nama bulan dalam bahasa Indonesia
        function getBulanIndonesia(month) {
            const bulan = ["JANUARI", "FEBRUARI", "MARET", "APRIL", "MEI", "JUNI", "JULI", "AGUSTUS", "SEPTEMBER",
                "OKTOBER", "NOVEMBER", "DESEMBER"
            ];
            return bulan[month];
        }

        // Fungsi untuk mendapatkan tanggal dalam format DD MMMM YYYY
        function getTanggal(tanggal) {
            const bulan = getBulanIndonesia(tanggal.getMonth());
            const tahun = tanggal.getFullYear();
            return `${tanggal.getDate()} ${bulan} ${tahun}`;
        }

        // Fungsi untuk menampilkan hari dan tanggal di dalam elemen dengan id="tanggal-hari"
        function tampilkanHariTanggal(tanggal) {
            const hari = getHariIndonesia(tanggal.getDay());
            const tanggalFormatted = getTanggal(tanggal);
            const element = document.getElementById("tanggal-hari");
            element.innerHTML = `${hari}, ${tanggalFormatted}`;
        }

        // Fungsi untuk memperbarui tanggal yang ingin ditampilkan berdasarkan nilai input
        function updateTanggal() {
            const tanggalInput = new Date(document.getElementById('tanggal').value);
            sessionStorage.setItem('tanggalPilihan', tanggalInput); // Simpan tanggal yang dipilih di sessionStorage
            tampilkanHariTanggal(tanggalInput);
        }

        // Fungsi untuk me-refresh halaman ke tanggal hari ini
        function refreshHariIni() {
            sessionStorage.removeItem('tanggalPilihan'); // Hapus data dari sessionStorage
            location.reload(); // Me-refresh halaman
        }

        // Memanggil fungsi tampilkanHariTanggal saat halaman dimuat
        window.onload = function() {
            const tanggalHariIni = new Date();
            const tanggalDariSessionStorage = sessionStorage.getItem('tanggalPilihan');

            if (tanggalDariSessionStorage) {
                const tanggalPilihan = new Date(tanggalDariSessionStorage);
                document.getElementById('tanggal').valueAsDate = tanggalPilihan;
                tampilkanHariTanggal(tanggalPilihan);
            } else {
                document.getElementById('tanggal').valueAsDate = tanggalHariIni;
                tampilkanHariTanggal(tanggalHariIni);
            }
        };
    </script>

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
