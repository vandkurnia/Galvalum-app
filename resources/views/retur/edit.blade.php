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


                <form action="{{ route('retur.update', ['id_retur' => $dataRetur->id_retur]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id_pesanan" value="{{ $dataRetur->id_pesanan }}">

                    <div class="form-group">
                        <label for="tanggal_retur" class="form-label">Tanggal Retur</label>
                        <input type="date" class="form-control" id="tanggal_retur" name="tanggal_retur"
                            value="{{ date('Y-m-d', strtotime($dataRetur->tanggal_retur)) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="bukti" class="form-label">Bukti</label>
                        <input type="file" class="form-control" id="bukti" name="bukti" >
                    </div>
                    <div class="form-group">
                        <label for="jenis_retur" class="form-label">Jenis Retur</label>
                        <select class="form-control" id="jenis_retur" name="jenis_retur" required>
                            {{-- <option value=""></option> --}}
                            <option {{ $dataRetur->jenis_retur == 'Rusak' }} value="Rusak">Rusak</option>
                            <option {{ $dataRetur->jenis_retur == 'Tidak Rusak' }} value="Tidak Rusak">Tidak Rusak</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3"> {{ $dataRetur->keterangan }}</textarea>
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
