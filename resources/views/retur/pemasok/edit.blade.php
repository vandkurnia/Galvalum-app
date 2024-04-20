@extends('app')

@section('title', 'Stok barang')
@section('header-custom')



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
                <h6 class="m-0 font-weight-bold text-primary">Edit Ajuan Retur</h6>
            </div>
            <div class="card-body">


                <form action="{{ route('retur.pembeli.update', $retur->id_retur_pemasok) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="no_retur_pemasok">No. Retur Pemasok</label>
                        <input type="text" class="form-control" id="no_retur_pemasok" name="no_retur_pemasok"
                            value="{{ $retur->no_retur_pemasok }}" required>
                    </div>
                    <div class="form-group">
                        <label for="faktur_retur_pemasok">Faktur Retur Pemasok</label>
                        <input type="text" class="form-control" id="faktur_retur_pemasok" name="faktur_retur_pemasok"
                            value="{{ $retur->faktur_retur_pemasok }}" required>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_retur">Tanggal Retur</label>
                        <input type="date" class="form-control" id="tanggal_retur" name="tanggal_retur"
                            value="{{ $retur->tanggal_retur }}" required>
                    </div>
                    <div class="form-group">
                        <label for="bukti_retur_pemasok">Bukti Retur Pemasok</label>
                        <input type="file" class="form-control" id="bukti_retur_pemasok" name="bukti_retur_pemasok">
                    </div>
                    <div class="form-group">
                        <label for="jenis_retur">Jenis Retur</label>
                        <select class="form-control" id="jenis_retur" name="jenis_retur" required>
                            <option value="Rusak" {{ $retur->jenis_retur == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                            <option value="Tidak Rusak" {{ $retur->jenis_retur == 'Tidak Rusak' ? 'selected' : '' }}>Tidak
                                Rusak</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="total_nilai_retur">Total Nilai Retur</label>
                        <input type="number" step="0.01" class="form-control" id="total_nilai_retur"
                            name="total_nilai_retur" value="{{ $retur->total_nilai_retur }}" required>
                    </div>
                    <div class="form-group">
                        <label for="pengembalian_data">Pengembalian Data</label>
                        <textarea class="form-control" id="pengembalian_data" name="pengembalian_data" rows="3">{{ $retur->pengembalian_data }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="kekurangan">Kekurangan</label>
                        <textarea class="form-control" id="kekurangan" name="kekurangan" rows="3">{{ $retur->kekurangan }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="Belum Selesai" {{ $retur->status == 'Belum Selesai' ? 'selected' : '' }}>Belum
                                Selesai</option>
                            <option value="Selesai" {{ $retur->status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="id_pemasok">Pemasok</label>
                        <select class="form-control" id="id_pemasok" name="id_pemasok" required>
                            @foreach ($dataPemasok as $pemasok)
                                <option value="{{ $pemasok->id_pemasok }}"
                                    {{ $pemasok->id_pemasok == $retur->id_pemasok ? 'selected' : '' }}>
                                    {{ $pemasok->nama_pemasok }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>


            </div>
        </div>
    </div>


@endsection







@section('javascript-custom')

@endsection
