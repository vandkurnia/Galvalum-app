@extends('app')

@section('title', 'Retur Stok Barang')
@section('header-custom')
    <!-- Filepond stylesheet -->
    <link href="{{ secure_asset('library/filepond/dist/filepond.css') }}" rel="stylesheet">
    {{-- Filepond image preview --}}
    <link href="{{ secure_asset('library/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css') }}"
        rel="stylesheet" />
    <link href="{{ secure_asset('library/filepond-plugin-image-edit/dist/filepond-plugin-image-edit.css') }}"
        rel="stylesheet" />
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
            <div class="card-header py-4">

                <div class="container mt-5">
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-triangle fa-2x mr-3"></i>
                        <div>
                            <strong>Halaman ini sedang dalam perbaikan dan migrasi data</strong> dan tidak menyimpan
                            perubahan hingga <span
                                id="maintenance-date">{{ date('d-m-Y H:i:s',strtotime('2024-05-23 20:00:00')) }}</span>
                            atau lebih cepat.
                        </div>
                    </div>
                </div>
                <h6 class="m-0 font-weight-bold text-primary">Retur</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="Retur">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                {{-- <th>Tipe</th> --}}
                                <th>Qty Tersedia</th>
                                <th>Qty Retur</th>
                                <th>Harga Pemasok</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr data-id-barang="{{ $dataBarang->hash_id_barang }}">
                                <td>{{ 1 }}</td>
                                <td>{{ $dataBarang->nama_barang }}</td>
                                {{-- <td>{{ $dataBarang-> }}</td> --}}
                                <td><span class="qty-available">{{ $totalStok }}</span></td>
                                <td>
                                    <input type="number" class="form-control qty-retur" min="0" max="10"
                                        value="0" oninput="calculateTotal(this)">
                                </td>
                                <td><span class="harga-pemasok">{{ (int) $dataBarang->harga_barang_pemasok }}</span></td>
                                <td><span class="total">0</span></td>
                            </tr>

                            <!-- Add more rows as needed -->
                        </tbody>
                    </table>
                </div>
                <div class="mt-2">Total Pembayaran: <span id="total_pembayaran">Rp. 0</span></div>
            </div>
        </div>


        <script>
            function calculateTotal(input) {
                const row = input.closest('tr');
                const qtyAvailable = parseInt(row.querySelector('.qty-available').innerText);
                const qtyRetur = parseInt(input.value);
                const hargaPemasok = parseInt(row.querySelector('.harga-pemasok').innerText);
                const totalCell = row.querySelector('.total');

                if (qtyRetur > qtyAvailable) {
                    input.value = qtyAvailable;
                }

                const newQtyRetur = parseInt(input.value);
                const total = hargaPemasok * newQtyRetur;
                totalCell.innerText = total;

                updateTotalPembayaran();
            }

            function updateTotalPembayaran() {
                const totalCells = document.querySelectorAll('.total');
                let totalPembayaran = 0;

                totalCells.forEach(cell => {
                    totalPembayaran += parseInt(cell.innerText);
                });

                document.getElementById('total_pembayaran').innerText = `Rp. ${totalPembayaran}`;
            }
        </script>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Detail Retur</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('retur.pemasok.store') }}" method="POST" id="formRetur"
                    enctype="multipart/form-data">
                    <!-- CSRF Token (static value if needed) -->
                    @csrf
                    <input type="hidden" name="id_barang" value="{{ $id_barang }}">

                    <div class="form-group">
                        <label for="tanggalReturPemasok">Tanggal Retur Pembeli</label>
                        <input type="date" class="form-control" id="tanggalReturPemasok" name="tanggal_retur_pemasok"
                            value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="bukti_retur_pemasok">Bukti Retur Pembeli</label>
                        <input type="file" name="bukti_retur_pemasok" class="filepond" data-max-file-size="10MB"
                            id="BuktiReturPemasok" required>
                    </div>

                    <div class="form-group">
                        <label for="jenis_retur">Jenis Retur</label>
                        <select class="form-control" id="jenis_retur" name="jenis_retur" required>
                            <option value="Rusak">Rusak</option>
                            <option value="Tidak Rusak">Tidak Rusak</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-primary mt-4 float-right"
                            onclick="kirimPesanan()">Retur</button>
                    </div>
                </form>
            </div>
        </div>


    </div>

@endsection







@section('javascript-custom')

    <script src="{{ secure_asset('library/filepond-plugin-image-edit/dist/filepond-plugin-image-edit.js') }}"></script>
    <script
        src="{{ secure_asset('library/filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.js') }}">
    </script>
    <script src="{{ secure_asset('library/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js') }}">
    </script>
    <!-- add before </body> -->
    <script
        src="{{ secure_asset('library/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js') }}">
    </script>
    <script src="{{ secure_asset('library/filepond-plugin-file-encode/dist/filepond-plugin-file-encode.js') }}"></script>
    <!-- Load FilePond library -->
    <script src="{{ secure_asset('library/filepond/dist/filepond.js') }}"></script>


    <script>
        // We want to preview images, so we register
        // the Image Preview plugin, We also register 
        // exif orientation (to correct mobile image
        // orientation) and size validation, to prevent
        // large files from being added
        FilePond.registerPlugin(
            FilePondPluginImagePreview,
            FilePondPluginImageExifOrientation,
            FilePondPluginFileValidateSize,
            FilePondPluginImageEdit,
            FilePondPluginFileEncode
        );

        // FilePond.setOptions({
        //     styleItemPanelAspectRatio: null // Ukuran gambar menyesuaikan dengan kotak preview
        // });
        // Select the file input and use 
        // create a FilePond instance at the fieldset element location

        const pond = FilePond.create(
            document.querySelector('#BuktiReturPemasok'), {
                imagePreviewMinHeight: 80

            }
        );
    </script>


    <script>
        function kirimPesanan() {
            const rows = document.querySelectorAll('#Retur tbody tr');
            const returData = [];

            rows.forEach(row => {
                const idBarang = row.getAttribute('data-id-barang');
                const qtyRetur = row.querySelector('.qty-retur').value;
                const hargaPemasok = row.querySelector('.harga-pemasok').textContent;
                const total = row.querySelector('.total').textContent;

                if (qtyRetur > 0) {
                    returData.push({
                        id_barang: idBarang,
                        qty: qtyRetur,
                        harga: hargaPemasok,
                        total: total
                    });
                }
            });

            const form = document.getElementById('formRetur');
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'retur_data';
            input.value = JSON.stringify(returData);
            form.appendChild(input);

            form.submit();
        }
    </script>


@endsection
