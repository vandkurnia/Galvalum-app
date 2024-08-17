@extends('app')

@section('title', 'Hutang dan Stok')
@section('header-custom')
    <link href="{{ secure_asset('library/datatable/datatables.min.css') }}" rel="stylesheet">
@endsection

@section('content')
 <!-- Begin Page Content -->
 <div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Hutang dan Stok</h6>
        </div>
        <div class="card-body">
            <div class="mb-3">
                @if (Auth::user()->role == 'admin')
                    <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#TambahHutangStok"><i
                            class="fa fa-plus"></i> Tambah Hutang dan Stok</button>
                @endif
            </div>
            <div class="table-responsive">
                <table class="table" id="hutangdanstok" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Total</th>
                            <th>DP</th>
                            <th>Nominal Terbayar</th>
                            <th>Stok</th>
                            <th>Buku Besar</th>
                            <th data-orderable="false">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($hutangDanStok as $index => $item)
                            <tr>
                                <th>{{ $index + 1 }}</th>
                                <td>{{ number_format($item->total, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->dp, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->nominal_terbayar, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->stok, 1, '.', '') }}</td>
                                <td>{{ $item->bukubesar->nama ?? '-' }}</td>
                                <td>
                                    @if (Auth::user()->role == 'admin')
                                        <a href="{{ route('hutang-dan-stok.show', $item->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-primary btn-sm"
                                            onclick="funcEditHutangStok('{{ route('hutang-dan-stok.edit', $item->id) }}')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm"
                                            onclick="funcHapusHutangStok('{{ route('hutang-dan-stok.destroy', $item->id) }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="1">Total</th>
                            <th>{{ number_format($hutangDanStok->sum('total'), 0, ',', '.') }}</th>
                            <th>{{ number_format($hutangDanStok->sum('dp'), 0, ',', '.') }}</th>
                            <th>{{ number_format($hutangDanStok->sum('nominal_terbayar'), 0, ',', '.') }}</th>
                            <th>{{ number_format($hutangDanStok->sum('stok'), 1, '.', '') }}</th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
 </div>
@endsection

@section('javascript-custom')
    <!-- Tambahkan script JavaScript yang diperlukan di sini -->
@endsection