@extends('app')

@section('title', 'Hutang')
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
                {{-- <div class="container mt-5">
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-triangle fa-2x mr-3"></i>
                        <div>
                            <strong>Halaman ini sedang dalam perbaikan dan migrasi data</strong> dan tidak menyimpan perubahan hingga <span
                                id="maintenance-date">{{ date('d-m-Y H:i:s', strtotime(strtotime('2024-05-23 20:00:00'))) }}</span> atau lebih cepat.
                        </div>
                    </div>
                </div> --}}

                {{-- <script>
                    // Set the maintenance date here
                    var underMaintenanceDate = '2024-06-01'; // Example date

                    // Format the date for display
                    var options = {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    };
                    var maintenanceDate = new Date(underMaintenanceDate).toLocaleDateString('id-ID', options);

                    // Display the date in the alert
                    document.getElementById('maintenance-date').textContent = maintenanceDate;
                </script> --}}

                <h6 class="m-0 font-weight-bold text-primary">Laporan Hutang</h6>
                <!-- Filter Tanggal -->
                <div class="form-group">
                    <label for="tanggal">Filter Tanggal:</label>
                    <input type="date" id="tanggal" name="tanggal" class="form-control">
                </div>
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-bordered" id="laporanHutang" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Supplier</th>
                                <th>Barang Pesanan</th>
                                <th>Total Pemesanan</th>
                                <th>Tanggal Stock</th>
                                <th>Harga Bayar</th>
                                <th>Jumlah Terbayar</th>
                                <th>Kekurangan</th>
                                <th>Jatuh Tempo</th>
                                <th>Status</th>
                                <th>Lunas</th>
                                <th>Update</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataLaporanHutang as $laporan)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $laporan['nama_pemasok'] }}</td>
                                    <td>{{ $laporan['nama_barang'] }}</td>
                                    <td>{{ $laporan['total_pesanan'] }}</td>
                                    <td>{{ date('Y-m-d', strtotime($laporan['tanggal_stok'])) }}</td>
                                    <td>{{ (int) $laporan['harga_bayar'] }}</td>
                                    <td>{{ (int) $laporan['jumlah_terbayar'] }}</td>
                                    <td>{{ $laporan['harga_bayar'] - $laporan['jumlah_terbayar'] }}</td>
                                    <td>{{ date('Y-m-d', strtotime($laporan['jatuh_tempo'])) }}</td>
                                    <td><span class="badge badge-warning">Belum Lunas</span></td>
                                    <td>Unpaid</td>
                                    <td><a href="{{ route('cicilan.hutang.index', ['id_barang' => $laporan['id_barang']]) }}"
                                            class="btn btn-primary">Update cicilan</a></td>
                                </tr>
                            @endforeach

                            {{-- <tr>
                                <td>2</td>
                                <td>Supplier B</td>
                                <td>Barang B</td>
                                <td>$200</td>
                                <td>2024-05-05</td>
                                <td>$200</td>
                                <td>$200</td>
                                <td>$0</td>
                                <td>2024-06-05</td>
                                <td><span class="badge badge-success">Lunas</span></td>
                                <td>Paid</td>
                                <td><button class="btn btn-primary">Update</button></td>
                            </tr> --}}
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





@section('javascript-custom')
    <script src="{{ secure_asset('library/datatable/datatables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#laporanHutang').DataTable();
        });
    </script>
@endsection
