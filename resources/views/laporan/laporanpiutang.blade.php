@extends('app')

@section('title', 'Piutang')
@section('header-custom')
    <link href="{{ secure_asset('library/datatable/datatables.min.css') }}" rel="stylesheet">



    <style>
        @keyframes blink-animation {
            0% {
                background-color: transparent;
            }

            50% {
                background-color: #bcc5e2;
            }

            100% {
                background-color: transparent;
            }
        }

        #laporanPiutang .highlight-row {
            animation: blink-animation 0.5s infinite;
            /* Ubah warna latar belakang sesuai kebutuhan */
            background: #bcc5e2;
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

        {{-- Error flashdata --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-exclamation-circle" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                    <path
                        d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z" />
                </svg>
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <div class="card shadow mb-4">
            <div class="card-header py-3">

                <h6 class="m-0 font-weight-bold text-primary">Laporan Piutang</h6>
                <form>
                    <div class="form-group">
                        <label for="tanggal">Filter Tanggal:</label>
                        <input type="date" id="tanggal" name="tanggal" class="form-control"
                            onchange="this.form.submit()">
                    </div>
                </form>

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
                                <tr class="piutang{{ $notaPembelian['id_nota'] }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $notaPembelian['nama_pembeli'] }}</td>
                                    <td>{{ $notaPembelian['no_hp_pembeli'] }}</td>
                                    <td>{{ number_format($notaPembelian['total_pembelian'], 1, ',', '.') }}</td>
                                    <td>{{ date('Y-m-d', strtotime($notaPembelian['tanggal_pembelian'])) }}</td>
                                    <td>{{ number_format($notaPembelian['total'], 0, ',', '.') }}</td>
                                    <td>{{ number_format($notaPembelian['terbayar'], 0, ',', '.') }}</td>
                                    <td>{{ number_format($notaPembelian['total'] - $notaPembelian['terbayar'], 0, ',', '.') }}
                                    </td>
                                    <td>{{ date('Y-m-d 00:00:00', strtotime($notaPembelian['jatuh_tempo'] ?? $notaPembelian['tanggal_pembelian'])) }}
                                    </td>
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
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5">Total</th>
                                <th id="totalHargaTotal">Rp. 0</th>
                                <th id="totalJumlahTerbayar">Rp. 0</th>
                                <th id="totalKekurangan">Rp. 0</th>
                                <th colspan="4"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">

                <h6 class="m-0 font-weight-bold text-primary">Lunas dan Kelebihan</h6>
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-bordered" id="laporanPiutangLunas" width="100%" cellspacing="0">
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
                            @foreach ($NotaPembelianLunasdanKelebihan as $notaLunasdanKelebihan)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $notaLunasdanKelebihan['nama_pembeli'] }}</td>
                                    <td>{{ $notaLunasdanKelebihan['no_hp_pembeli'] }}</td>
                                    {{-- <td>{{ "Barang Pembelian" }}</td> --}}
                                    <td>{{ number_format($notaLunasdanKelebihan['total_pembelian'], 1, ',', '.') }}</td>
                                    {{-- <td>{{ $notaLunasdanKelebihan['jenis_pelanggan'] }}</td> --}}
                                    <td>{{ date('Y-m-d', strtotime($notaLunasdanKelebihan['tanggal_pembelian'])) }}</td>
                                    <td>{{ number_format($notaLunasdanKelebihan['total'], 0, ',', '.') }}</td>
                                    <td>{{ number_format($notaLunasdanKelebihan['terbayar'], 0, ',', '.') }}</td>
                                    <td>{{ number_format($notaLunasdanKelebihan['total'] - $notaLunasdanKelebihan['terbayar'], 0, ',', '.') }}
                                    </td>
                                    <td>{{ date('Y-m-d 00:00:00', strtotime($notaLunasdanKelebihan['jatuh_tempo'] ?? $notaLunasdanKelebihan['tanggal_pembelian'])) }}
                                    </td>
                                    <td>
                                        @if ($notaLunasdanKelebihan['status_bayar'] == 'Lunas')
                                            <span
                                                class="badge badge-success">{{ $notaLunasdanKelebihan['status_bayar'] }}</span>
                                        @elseif ($notaLunasdanKelebihan['status_bayar'] == 'Belum Lunas')
                                            <span
                                                class="badge badge-danger">{{ $notaLunasdanKelebihan['status_bayar'] }}</span>
                                        @elseif ($notaLunasdanKelebihan['status_bayar'] == 'Kelebihan')
                                            <span
                                                class="badge badge-warning">{{ $notaLunasdanKelebihan['status_bayar'] }}</span>
                                        @else
                                            {{ $notaLunasdanKelebihan['status_bayar'] }}
                                        @endif
                                    </td>
                                    <td>Hutang</td>
                                    <td> <a href="{{ route('cicilan.index', ['id_nota' => $notaLunasdanKelebihan['id_nota']]) }}"
                                            class="btn btn-primary">
                                            <i class="fas fa-info-circle"></i> <!-- Ikon detail -->
                                        </a>
                                        <a href="{{ route('cicilan.notvisible', ['id_nota' => $notaLunasdanKelebihan['id_nota']]) }}"
                                            class="btn btn-danger">
                                            <i class="fas fa-trash-alt"></i> <!-- Ikon tong sampah -->
                                        </a>
                                    </td>

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
                        <tfoot>
                            <tr>
                                <th colspan="5">Total</th>
                                <th id="totalHargaTotalLunas">Rp. 0</th>
                                <th id="totalJumlahTerbayarLunas">Rp. 0</th>
                                <th id="totalKekuranganLunas">Rp. 0</th>
                                <th colspan="4"></th>
                            </tr>
                        </tfoot>
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
            $('#laporanPiutang').DataTable({
                drawCallback: function() {
                    var api = this.api();

                    // Function to calculate column totals
                    function getColumnTotal(columnIndex) {
                        return api.column(columnIndex, {
                            page: 'current'
                        }).data().reduce(function(sum, value) {
                            return sum + parseInt(value.replace(/[\Rp.]/g, '').replace(/,/g,
                                ''), 10);
                        }, 0);
                    }

                    // Calculate totals for each column
                    var totalHargaTotal = getColumnTotal(5);
                    var totalJumlahTerbayar = getColumnTotal(6);
                    var totalKekurangan = getColumnTotal(7);

                    // Update the footer with formatted totals
                    $('#totalHargaTotal').text('Rp. ' + totalHargaTotal.toLocaleString('id-ID'));
                    $('#totalJumlahTerbayar').text('Rp. ' + totalJumlahTerbayar.toLocaleString(
                        'id-ID'));
                    $('#totalKekurangan').text('Rp. ' + totalKekurangan.toLocaleString('id-ID'));
                }
            });
            $('#laporanPiutangLunas').DataTable({
                drawCallback: function() {
                    var api = this.api();

                    function getColumnTotal(columnIndex) {
                        return api.column(columnIndex, {
                            page: 'current'
                        }).data().reduce(function(sum, value) {
                            return sum + parseInt(value.replace(/[\Rp.]/g, '').replace(/,/g,
                                ''), 10);
                        }, 0);
                    }

                    // Calculate totals for each column
                    var totalHargaTotal = getColumnTotal(5);
                    var totalJumlahTerbayar = getColumnTotal(6);
                    var totalKekurangan = getColumnTotal(7);

                    // Update the footer with formatted totals
                    $('#totalHargaTotalLunas').text('Rp. ' + totalHargaTotal.toLocaleString('id-ID'));
                    $('#totalJumlahTerbayarLunas').text('Rp. ' + totalJumlahTerbayar.toLocaleString(
                        'id-ID'));
                    $('#totalKekuranganLunas').text('Rp. ' + totalKekurangan.toLocaleString('id-ID'));
                }
            });

            // Ambil nilai parameter solve dari URL
            const urlParams = new URLSearchParams(window.location.search);
            const solveValue = urlParams.get('solve');

            // Cari elemen <tr> dengan data-id yang sama dengan nilai solve
            const targetTr = $('#laporanPiutang .piutang' + parseInt(solveValue) + '');
            console.log(targetTr);

            // Lakukan sesuatu dengan targetTr jika ditemukan
            if (targetTr.length > 0) {
                // Misalnya, tambahkan class untuk menyorot atau lakukan operasi lain
                targetTr.addClass('highlight-row');

                // Scroll ke elemen targetTr jika perlu
                $('html, body').animate({
                    scrollTop: targetTr.offset().top - 100 // Sesuaikan offset jika perlu
                }, 1000); // Durasi animasi scroll dalam milidetik
            } else {
                console.log('Tidak ada elemen <tr> dengan data-id=' + solveValue);
            }
        });
    </script>

    <script></script>
@endsection
