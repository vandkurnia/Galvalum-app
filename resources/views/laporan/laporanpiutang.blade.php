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

                <h6 class="m-0 font-weight-bold text-primary">Laporan Piutang</h6>
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-bordered" id="laporanPiutang" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Pembeli</th>
                                <th>Handphone</th>
                                {{-- <th>Barang Pembelian</th> --}}
                                <th>Jumlah Pembelian</th>
                                {{-- <th>Jenis Pelanggan</th> --}}
                                <th>Tanggal Pembelian</th>
                                <th>Harga Total</th>
                                <th>Jumlah Terbayar</th>
                                <th>Kekurangan</th>
                                <th>Jatuh Tempo</th>
                                <th>Status</th>
                                <th>Lunas</th>
                                <th>Update</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataNotaPembelian as $notaPembelian)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $notaPembelian['nama_pembeli'] }}</td>
                                    <td>{{ $notaPembelian['no_hp_pembeli'] }}</td>
                                    {{-- <td>{{ "Barang Pembelian" }}</td> --}}
                                    <td>{{ (int) $notaPembelian['total_pembelian'] }}</td>
                                    {{-- <td>{{ $notaPembelian['jenis_pelanggan'] }}</td> --}}
                                    <td>{{ date('Y-m-d', strtotime($notaPembelian['tanggal_pembelian'])) }}</td>
                                    <td>{{ number_format($notaPembelian['total'], 0, ',', '.') }}</td>
                                    <td>{{ number_format($notaPembelian['terbayar'], 0, ',', '.') }}</td>
                                    <td>{{ number_format($notaPembelian['total'] - $notaPembelian['terbayar'], 0, ',', '.') }}
                                    </td>
                                    <td>{{ date('Y-m-d 00:00:00', strtotime($notaPembelian['jatuh_tempo'])) }}</td>
                                    <td>
                                        @if ($notaPembelian['status_bayar'] == 'Lunas')
                                            <span class="badge badge-success">{{ $notaPembelian['status_bayar'] }}</span>
                                        @elseif ($notaPembelian['status_bayar'] == 'Belum Lunas')
                                            <span class="badge badge-danger">{{ $notaPembelian['status_bayar'] }}</span>
                                        @elseif ($notaPembelian['status_bayar'] == 'Kelebihan')
                                            <span class="badge badge-warning">{{ $notaPembelian['status_bayar'] }}</span>
                                        @else
                                            {{ $notaPembelian['status_bayar'] }}
                                        @endif
                                    </td>
                                    <td>Hutang</td>
                                    <td><a href="{{ route('cicilan.index', ['id_nota' => $notaPembelian['id_nota']]) }}"
                                            class="btn btn-primary">Update cicilan</a></td>
                                </tr>
                            @endforeach

                            {{-- <tr>
                                <td>2</td>
                                <td>Pembeli B</td>
                                <td>08123456788</td>
                                <td>Barang B</td>
                                <td>20</td>
                                <td>Pelanggan B</td>
                                <td>2024-05-05</td>
                                <td>$200</td>
                                <td>$200</td>
                                <td>$0</td>
                                <td>2024-06-05</td>
                                <td><span class="badge badge-success">Lunas</span></td>
                                <td>Dibayar</td>
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
            $('#laporanPiutang').DataTable();
        });
    </script>
@endsection
