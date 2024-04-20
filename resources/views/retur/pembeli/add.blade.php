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
                <h6 class="m-0 font-weight-bold text-primary">Ajuan Retur</h6>
            </div>
            <div class="card-body">


                <form action="{{ route('retur.pembeli.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id_pesanan" value="{{ $id_pesanan }}">

                    {{-- <input type="hidden" name="id_pembeli" value="{{ $id_pembeli }}"> --}}

             
                    <div class="form-group">
                        <label for="no_retur_pembeli">No. Retur Pembeli</label>
                        <input type="text" class="form-control" id="no_retur_pembeli" name="no_retur_pembeli" required>
                    </div>
                    <div class="form-group">
                        <label for="faktur_retur_pembeli">Faktur Retur Pembeli</label>
                        <input type="text" class="form-control" id="faktur_retur_pembeli" name="faktur_retur_pembeli"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_retur_pembeli">Tanggal Retur Pembeli</label>
                        <input type="date" class="form-control" id="tanggal_retur_pembeli" name="tanggal_retur_pembeli"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="bukti_retur_pembeli">Bukti Retur Pembeli</label>
                        <input type="file" class="form-control" id="bukti_retur_pembeli" name="bukti_retur_pembeli"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="jenis_retur">Jenis Retur</label>
                        <select class="form-control" id="jenis_retur" name="jenis_retur" required>
                            <option value="Rusak">Rusak</option>
                            <option value="Tidak Rusak">Tidak Rusak</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="total_nilai_retur">Total Nilai Retur</label>
                        <input type="number" step="0.01" class="form-control" id="total_nilai_retur"
                            name="total_nilai_retur" required>
                    </div>
                    <div class="form-group">
                        <label for="pengembalian_data">Pengembalian Data</label>
                        <textarea class="form-control" id="pengembalian_data" name="pengembalian_data" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="kekurangan">Kekurangan</label>
                        <textarea class="form-control" id="kekurangan" name="kekurangan" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="Belum Selesai">Belum Selesai</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="id_pembeli">Pembeli</label>
                        <select class="form-control" id="id_pembeli" name="id_pembeli" required>
                            @foreach ($dataPembeli as $pembeli)
                                <option value="{{ $pembeli->id_pembeli }}">{{ $pembeli->nama_pembeli }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>


            </div>
        </div>
    </div>


@endsection







@section('javascript-custom')

@endsection
