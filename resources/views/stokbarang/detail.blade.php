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
                        {{-- <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#TambahHutangStok"><i
                            class="fa fa-plus"></i> Tambah Hutang dan Stok</button> --}}
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
                                    <td>
                                        @if (Auth::user()->role == 'admin')
                                           

                                            <!-- Tombol Hapus -->
                                            <button class="btn btn-danger btn-sm"
                                                onclick="funcHapusHutangStok(`{{ route('hutang-dan-stok.destroy', ['id' => $item->id_hutang_stok]) }}`)">
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



    {{-- Modal Section --}}


    <!-- Modal Edit -->
    <div class="modal fade" id="editHutangStokModal" tabindex="-1" aria-labelledby="editHutangStokModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editHutangStokModalLabel">Edit Hutang dan Stok</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editHutangStokForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="total">Total</label>
                            <input type="number" class="form-control" id="total" name="total" required>
                        </div>
                        <div class="form-group">
                            <label for="dp">DP</label>
                            <input type="number" class="form-control" id="dp" name="dp">
                        </div>
                        <div class="form-group">
                            <label for="nominal_terbayar">Nominal Terbayar</label>
                            <input type="number" class="form-control" id="nominal_terbayar" name="nominal_terbayar">
                        </div>
                        <div class="form-group">
                            <label for="stok">Stok</label>
                            <input type="number" class="form-control" id="stok" name="stok" required>
                        </div>
                        <input type="hidden" id="id_bukubesar" name="id_bukubesar">
                        <input type="hidden" id="id_barang" name="id_barang">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Modal Hapus -->
    <div class="modal fade" id="hapusHutangStokModal" tabindex="-1" aria-labelledby="hapusHutangStokModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="hapusHutangStokModalLabel">Hapus Hutang dan Stok</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="hapusHutangStokForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menghapus data ini?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>




@endsection

@section('javascript-custom')
    <!-- Tambahkan script JavaScript yang diperlukan di sini -->

    <script>
        function funcEditHutangStok(url) {
           
            // Mengambil data dari server menggunakan AJAX
            $.get(url, function(data) {
                // Isi form di modal edit dengan data yang didapat
                $('#total').val(data.total);
                $('#dp').val(data.dp);
                $('#nominal_terbayar').val(data.nominal_terbayar);
                $('#stok').val(data.stok);
                $('#id_bukubesar').val(data.id_bukubesar);
                $('#id_barang').val(data.id_barang);

                // Set action pada form agar mengarah ke route update
                $('#editHutangStokForm').attr('action', data.updateUrl);

                // Tampilkan modal edit
                $('#editHutangStokModal').modal('show');
            });
        }

        function funcHapusHutangStok(url) {
            // Set action pada form hapus agar mengarah ke route destroy
            $('#hapusHutangStokForm').attr('action', url);

            // Tampilkan modal hapus
            $('#hapusHutangStokModal').modal('show');
        }
    </script>
@endsection
