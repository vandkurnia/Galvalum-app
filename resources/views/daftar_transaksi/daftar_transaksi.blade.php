@extends('app')

@section('title', 'Daftar Transaksi')

@section('header-custom')
    <link href="{{ secure_asset('library/datatable/datatables.min.css') }}" rel="stylesheet">



@endsection

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Data Transaksi</h6>
                <form>
                    <div class="form-group">
                        <label for="tanggal">Filter Tanggal:</label>
                        <input type="date" id="tanggal" name="tanggal" class="form-control">
                    </div>
                </form>
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
                                {{-- <th>Jenis Pelanggan</th> --}}
                                <th>Total</th>
                                <th>Lunas</th>
                                <th>Waktu Payment</th>
                                <th>Payment Methode</th>
                                <th data-orderable="false">Cetak</th>
                                <th data-orderable="false">Aksi</th>


                                <th data-orderable="false">
                                    @if (Auth::user()->role == 'admin')
                                        Log
                                    @else
                                        -
                                    @endif
                                </th>

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
                                    {{-- <td>Unknown</td> --}}
                                    <td>{{ $notaPembeli['total'] }}</td>
                                    <td>{{ $notaPembeli['status_pembayaran'] }}</td>
                                    <td>{{ date('Y-m-d', strtotime($notaPembeli['created_at'])) }}</td>
                                    <td>{{ $notaPembeli['metode_pembayaran'] }}</td>
                                    {{-- <td><a href="{{ route('pemesanan.penjualanPDF', ['id' => $notaPembeli['id_nota']]) }}"
                                            class="btn btn-info btn-sm">
                                            Penjualan</a>
                                        <a href="{{ route('pemesanan.suratjalanPDF', ['id' => $notaPembeli['id_nota']]) }}"
                                            class="btn btn-primary btn-sm">
                                            Surat Jalan</a>
                                    </td> --}}
                                    <td>
                                        <button class="btn btn-warning"
                                            onclick="print('{{ route('cetak.invoice', ['no_nota' => $notaPembeli['no_nota']]) }}', '{{ route('cetak.surat-jalan', ['no_nota' => $notaPembeli['no_nota']]) }}')">
                                            <i class="fas fa-print"></i></button>
                                    </td>
                                    <td>
                                        <a href="{{ route('retur.pembeli.add', ['id_nota' => $notaPembeli['id_nota']]) }}"
                                            class="btn btn-info btn-sm">Retur</a>
                                        {{-- <button class="btn btn-info btn-sm p-2"
                                            onclick="funcInfoNota('{{ route('pemesanan.infobarang', ['id' => $notaPembeli['id_nota']]) }}')"><i
                                                class="fas fa-info-circle"></i></button> --}}
                                        @if (Auth::user()->role == 'admin')
                                            <a href="{{ route('pemesanan.edit', ['id' => $notaPembeli['id_nota']]) }}"
                                                class="btn btn-primary btn-sm"><i class="fas fa-edit"></i>
                                                Edit</a>
                                            <button class="btn btn-danger btn-sm"
                                                onclick="funcHapusUser('{{ route('pemesanan.destroy', ['id' => $notaPembeli['id_nota']]) }}', 0)"><i
                                                    class="fas fa-trash"></i>
                                                Delete</button>
                                        @endif
                                    </td>


                                    <td>
                                        @if (Auth::user()->role == 'admin')
                                            <a href="{{ route('log-nota.index', ['id_nota' => $notaPembeli['id_nota']]) }}"
                                                class="btn btn-info btn-sm">
                                                <i class="fas fa-info-circle"></i>
                                            </a>
                                        @endif
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
    <script src="{{ secure_asset('library/datatable/datatables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Get the current URL and append `?api=yes`
            let currentUrl = window.location.href;

            if (currentUrl.indexOf('?') === -1) {
                currentUrl += '?api=yes';
            } else {
                currentUrl += '&api=yes';
            }

            // Set the default date to today (Y-m-d format)
            let today = new Date().toISOString().slice(0, 10);
            $('#tanggal').val(today);

            // Initialize the DataTable
            const dataTransaksi = $('#dataTransaksi').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: currentUrl,
                    type: 'GET',
                    data: function(d) {
                        d.dateFilter = $('#tanggal').val();
                    }
                },
                columns: [

                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            // `meta.row` is the index of the row
                            return meta.row + 1; // Add 1 to start numbering from 1
                        }
                    },
                    {
                        
                        data: 'no_nota',
                        orderable: false,
                        name: 'no_nota',
             
                    },
                    {
                        data: 'pembeli.no_hp_pembeli',
                        orderable: false,
                        name: 'pembeli.no_hp_pembeli'
                    },
                    {
                        data: 'pembeli.nama_pembeli',
                        orderable: false,
                        name: 'pembeli.nama_pembeli'
                    },
                    {
                        data: 'total_pesanan',
                        orderable: false,
                        name: 'total_pesanan'
                    },
                    {
                        data: 'created_at',
                        orderable: false,
                        name: 'created_at',
                        render: function(data) {
                            return new Date(data).toISOString().slice(0, 10);
                        }
                    },
                    {
                        data: 'created_at',
                        orderable: false,
                        name: 'created_at',
                        render: function(data) {
                            return new Date(data).toISOString().slice(11, 16);
                        }
                    },
                    {
                        data: 'total',
                        orderable: false,
                        name: 'total'
                    },
                    {
                        data: 'status_pembayaran',
                  
                        name: 'status_pembayaran',
                        orderable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data) {
                            return new Date(data).toISOString().slice(0, 10);
                        },
                        orderable: false
                    },
                    {
                        data: 'metode_pembayaran',
                       
                        name: 'metode_pembayaran'
                    },
                    {
                        data: "cetak_invoice",
                        orderable: false,
                        render: function(data, type, row) {
                            return data; // This will render the HTML buttons directly
                        }

                    },
                    {
                        data: "action_buttons",
                        orderable: false,
                        render: function(data, type, row) {
                            return data; // This will render the HTML buttons directly
                        }
                    },
                    {
                        data: "log_button",
                        orderable: false,
                        render: function(data, type, row) {
                            return data; // This will render the HTML buttons directly
                        }

                    }
                ]
            });
            // Reload DataTable when the date changes
            $('#tanggal').on('change', function() {
                dataTransaksi.ajax.reload();
            });

            // Reload DataTable when the search input changes
            $('#dataTransaksi_filter input').on('keyup', function() {
                dataTransaksi.search(this.value).draw();
            });
        });
    </script>
    <script>
        function print(urlinvoice, urlsuratjalan) {
            var w = 805;
            var h = 502;
            var left = (screen.width / 2) - (w / 2);
            var top = (screen.height / 2) - (h / 2);

            window.open(urlinvoice, 'invoice', 'width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
            window.open(urlsuratjalan, 'suratjalan ', 'width=' + w + ', height=' + h + ', top=' + top + ', left=' +
                left);
        }

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
