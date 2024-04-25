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




        /* Tampilan Table laba rugi */
        table thead {
            text-align: center;
            background-color: #7091e6;
            color: white;
        }

        .topic {
            font-weight: bold;
            background-color: grey;
            color: white;
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

                <h6 class="m-0 font-weight-bold text-primary">Laporan laba rugi</h6>
                <form>
                    <div class="form-group">
                        <label for="tanggal">Filter Tanggal:</label>
                        <input type="date" id="tanggal" name="tanggal" class="form-control" value="updateDate()">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary">Filter Tanggal</button>
                    </div>
                </form>
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-border">
                        <thead>
                            <tr>
                                <th colspan="3"> REKAP RINCIAN PENJUALAN </th>
                            </tr>
                            <tr>
                                <th colspan="3" id="tanggal-hari"></th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($penjualan_kotor as $pk)
                            <tr class="topic">
                                <th>PENJUALAN KOTOR</th>
                                <td></td>

                                <td>Rp {{ $pk->debit }}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td>MODAL</td>
                                <td></td>

                                <td>Rp 361.000 (+)</td>
                            </tr>
                            <tr>

                                <th></th>
                                <th></th>
                                <th>Rp 30.253.000</th>
                            </tr>
                            <tr>
                                <th>TAMBAHAN MODAL</th>
                                <td></td>
                                <td></td>
                            </tr>
                            @foreach($tambahan_modal as $th)
                            <tr>
                                <td>(+) {{ $th->jenis_modal_tambahan }}</td>
                                <td>Rp {{ $th->jumlah_modal }} (+)</td>
                                <td></td>
                            </tr>
                            @endforeach
                            <tr>
                                <th>JUMLAH TAMBAHAN MODAL</th>
                                <td></td>
                                <td>Rp {{$jumlah_tambahan_modal}} (+)</td>
                            </tr>
                            <tr class="topic">
                                <th>LABA KOTOR</th>
                                <th></th>
                                <th>Rp 36.254.000</th>
                            </tr>
                            <tr>
                                <th>PENGURANGAN/PENGELUARAN</th>
                                <td></td>
                                <td></td>
                            </tr>
                            @foreach($pengeluaran as $p)
                            <tr>
                                <td>(-) {{$p->nama_pengeluaran}}</td>
                                <td>Rp {{$p->jumlah_pengeluaran}} (+)</td>
                                <td></td>
                            </tr>
                            @endforeach
                            <tr>
                                <th>JUMLAH PENGURANGAN/PENGELUARAN</th>
                                <th></th>
                                <th>Rp {{$total_pengeluaran}} (-)</th>
                            </tr>
                            <tr class="topic">
                                <th>LABA BERSIH</th>
                                <th></th>
                                <th>Rp 22.863.000</th>
                            </tr>
                            <tr>
                                <th>(-) MODAL HARI SABTU 2/12/2023</th>
                                <th></th>
                                <th>Rp 463.000 (-)</th>
                            </tr>
                            <tr>
                                <th>TOTAL TRANSFER/SETOR TUNAI 1/12/2023</th>
                                <th></th>
                                <th>Rp 22.400.000</th>
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




@section('javascript-custom')

    <script>
        // Function to update the date input to today's date
        function updateDate() {
            var today = new Date();

            var year = today.getFullYear();
            var month = ('0' + (today.getMonth() + 1)).slice(-2); // Months are zero-based
            var day = ('0' + today.getDate()).slice(-2);

            var formattedDate = year + '-' + month + '-' + day;

            document.getElementById('tanggal').value = formattedDate;
        }

        // Call the function to set today's date initially
        updateDate();
    </script>

    <script>
    // Fungsi untuk mendapatkan hari dalam bahasa Indonesia
    function getHariIndonesia(day) {
        const hari = ["MINGGU", "SENIN", "SELASA", "RABU", "KAMIS", "JUMAT", "SABTU"];
        return hari[day];
    }

    // Fungsi untuk mendapatkan nama bulan dalam bahasa Indonesia
    function getBulanIndonesia(month) {
        const bulan = ["JANUARI", "FEBRUARI", "MARET", "APRIL", "MEI", "JUNI", "JULI", "AGUSTUS", "SEPTEMBER", "OKTOBER", "NOVEMBER", "DESEMBER"];
        return bulan[month];
    }

    // Fungsi untuk mendapatkan tanggal dalam format DD MMMM YYYY
    function getTanggal() {
        const sekarang = new Date();
        const tanggal = sekarang.getDate();
        const bulan = getBulanIndonesia(sekarang.getMonth());
        const tahun = sekarang.getFullYear();
        return `${tanggal} ${bulan} ${tahun}`;
    }

    // Fungsi untuk menampilkan hari dan tanggal saat ini di dalam elemen dengan id="tanggal-hari"
    function tampilkanHariTanggal() {
        const sekarang = new Date();
        const hari = getHariIndonesia(sekarang.getDay());
        const tanggal = getTanggal();
        const element = document.getElementById("tanggal-hari");
        element.innerHTML = `${hari}, ${tanggal}`;
    }

    // Memanggil fungsi tampilkanHariTanggal saat halaman dimuat
    window.onload = function() {
        tampilkanHariTanggal();
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
