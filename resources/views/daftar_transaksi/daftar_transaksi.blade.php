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
                 
                    <table class="table table-bordered" id="dataTransaksi" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Nota</th>
                                <th>Handphone</th>
                                <th>Nama</th>
                                <th>Jumlah Pembelian</th>
                                <th>Tanggal Beli</th>
                                <th>Jam</th>
                                <th>Jenis Pelanggan</th>
                                <th>Total</th>
                                <th>Lunas</th>
                                <th>Waktu Payment</th>
                                <th>Payment Methode</th>
                                <th>Cetak</th>
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
                                    <td>{{ $notaPembeli['no_nota'] }}</td>
                                    <td>{{ $notaPembeli['pembeli']['no_hp_pembeli'] }}</td>
                                    <td>{{ $notaPembeli['pembeli']['nama_pembeli'] }}</td>
                                
                                    <td>{{ $notaPembeli['total_pesanan'] }}</td>
                                    <td>{{ date('Y-m-d', strtotime($notaPembeli['created_at'])) }}</td>
                                    <td>{{ date('H:i', strtotime($notaPembeli['created_at'])) }}</td>
                                    <td>Unknown</td>
                                    <td>{{ $notaPembeli['total'] }}</td>
                                    <td>{{ $notaPembeli['status_pembayaran'] }}</td>
                                    <td>{{ date('Y-m-d', strtotime($notaPembeli['created_at'])) }}</td>
                                    <td>{{ $notaPembeli['metode_pembayaran'] }}</td>
                                    <td><a href="{{ route('pemesanan.penjualanPDF', ['id' => $notaPembeli['id_nota']]) }}"
                                            class="btn btn-info btn-sm">
                                            Penjualan</a>
                                            <a href="{{ route('pemesanan.suratjalanPDF', ['id' => $notaPembeli['id_nota']]) }}"
                                            class="btn btn-primary btn-sm">
                                            Surat Jalan</a></td>
                                    <td>
                                        <button class="btn btn-info btn-sm p-2"
                                            onclick="funcInfoNota('{{ route('pemesanan.infobarang', ['id' => $notaPembeli['id_nota']]) }}')"><i
                                                class="fas fa-info-circle"></i></button>


                                        <a href="{{ route('pemesanan.edit', ['id' => $notaPembeli['id_nota']]) }}"
                                            class="btn btn-primary btn-sm"><i class="fas fa-edit"></i>
                                            Edit</a>
                                        <button class="btn btn-danger btn-sm"
                                            onclick="funcHapusUser('{{ route('pemesanan.destroy', ['id' => $notaPembeli['id_nota']]) }}', 0)"><i
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

{{-- Modal Info Barang Nota --}}
<div class="modal fade" id="infoNotaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Informasi Barang nota pesanan</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">


            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>


{{-- End of Modal Info Barang Nota --}}

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>



@section('javascript-custom')

    <script>
        function funcInfoNota(url) {

            // Lakukan AJAX ke /user/
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    // Tampilkan modal dengan data response

                    if (response.code === 200) {
                        // Tampilkan data di dalam modal
                        $('#infoNotaModal .modal-body').html(response.data);
                        $('#infoNotaModal').modal('show');
                    } else {
                        // Tampilkan pesan error
                        console.error('Error:', response.message);
                    }

                },
                error: function(xhr, status, error) {
                    if (xhr.status === 401) {
                        // Unauthorized, reload halaman
                        window.location.reload(true);
                    } else {
                        console.error('Error:', error);
                    }
                }
            });
        }

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
