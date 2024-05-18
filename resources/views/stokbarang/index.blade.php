@extends('app')

@section('title', 'Stok barang')
@section('header-custom')

    <link rel="stylesheet" href="{{ secure_asset('library/datatables.net-dt/css/dataTables.dataTables.css') }}" />


@endsection

@section('content')
    <script src="{{ secure_asset('library/datatables.net-dt/js/dataTables.dataTables.js') }}"></script>

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
                <h6 class="m-0 font-weight-bold text-primary">Stok Barang</h6>
            </div>
            <div class="card-body">


                <div class="mb-3">
                    <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#TambahUser"><i
                            class="fa fa-plus"></i> Tambah Barang</button>
                </div>
                <div class="table-responsive">
                    <table class="table" id="stokbarang" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Pemasok</th>
                                <th>Nama Barang</th>
                                <th>Tipe Barang</th>
                                <th>Ukuran Barang</th>
                                <th>Harga Barang</th>
                                <th>Harga Supplier</th>
                                <th>Jumlah Stok</th>
                                <th>Retur</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        {{-- <tfoot>
                            <tr>
                                <th>No</th>
                                <th>Pemasok</th>
                                <th>Nama Barang</th>
                                <th>Tipe Barang</th>
                                <th>Ukuran Barang</th>
                                <th>Harga Barang</th>
                                <th>Jumlah Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </tfoot> --}}
                        <tbody>
                            @php

                                $no = 0;
                            @endphp

                            @foreach ($dataSemuaBarang as $databarang)
                                <tr>
                                    <th>{{ ++$no }}</th>
                                    <td>{{ isset($databarang->pemasok->nama_pemasok) ? $databarang->pemasok->nama_pemasok : '-' }}
                                    </td>
                                    <td>{{ $databarang->nama_barang }}</td>
                                    <td>{{ $databarang->tipeBarang->nama_tipe }}</td>
                                    <td>{{ $databarang->ukuran }}</td>
                                    <td>{{ number_format($databarang->harga_barang, 0, ',', '.') }}</td>
                                    <td>{{ number_format($databarang->harga_barang_pemasok, 0, ',', '.') }}</td>
                                    <td>{{ $databarang->stok }}</td>
                                    <td> <button class="btn btn-primary btn-sm"
                                            onclick="location.href='{{ route('retur.pemasok.add', ['id_pesanan' =>  $databarang->hash_id_barang]) }}'">
                                            Retur
                                        </button></td>
                                    <td>
                                        <button class="btn btn-primary"
                                            onclick="funcTambahStok('{{ route('stok.detail', ['id' => $databarang->hash_id_barang]) }}')">
                                            <i class="fa fa-plus"></i>
                                        </button>

                                        <button class="btn btn-danger"
                                            onclick="funcKurangStok('{{ route('stok.detail', ['id' => $databarang->hash_id_barang]) }}')">
                                            <i class="fa fa-minus"></i>
                                        </button>

                                        <button class="btn btn-primary"
                                            onclick="funcEditUser('{{ route('stok.edit', ['id' => $databarang->hash_id_barang]) }}')"><i
                                                class="fas fa-edit"></i></button>
                                        <button class="btn btn-danger"
                                            onclick="funcHapusUser('{{ route('stok.destroy', ['id' => $databarang->hash_id_barang]) }}', 0)"><i
                                                class="fas fa-trash"></i></button>
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

{{-- Modal Tambah User --}}
<div class="modal fade" id="TambahUser" tabindex="-1" role="dialog" aria-labelledby="tambahUser" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Barang</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('stok.store') }}" id="formTambahUser" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pemasok">Pemasok:</label>
                                <select class="form-control" id="pemasok" name="id_pemasok" required>
                                    <option value="">Tanpa Pemasok</option>
                                    @foreach ($dataPemasok as $pemasok)
                                        <option value="{{ $pemasok['id_pemasok'] }}">
                                            {{ $pemasok['nama_pemasok'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="kode_barang">Kode Barang</label>
                                <input id="kode_barang" type="text"
                                    class="form-control @error('kode_barang') is-invalid @enderror" name="kode_barang"
                                    value="{{ $kodeBarang }}" required>
                                @error('kode_barang')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="nama_barang">Nama Barang:</label>
                                <input type="text" class="form-control" name="nama_barang" id="nama_barang"
                                    placeholder="Nama Barang" required>
                            </div>
                            <div class="form-group">
                                <label for="ukuran">Ukuran Barang:</label>
                                <input type="text" class="form-control" name="ukuran" id="ukuran"
                                    placeholder="Ukuran Barang" required>
                            </div>
                            <div class="form-group">
                                <label for="id_tipe_barang">Tipe Barang:</label>
                                <select class="form-control" id="id_tipe_barang" name="id_tipe_barang" required>
                                    @foreach ($dataTipeBarang as $tipeBarang)
                                        <option value="{{ $tipeBarang['id_tipe_barang'] }}">
                                            {{ $tipeBarang['nama_tipe'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="harga_barang">Harga Jual:</label>
                                <input type="number" class="form-control" name="harga_barang" id="harga_barang"
                                    placeholder="Harga Barang" value="0" min="0" required>
                            </div>
                            <div class="form-group">
                                <label for="harga_barang_pemasok">Harga Barang Pemasok</label>
                                <input id="harga_barang_pemasok" type="number"
                                    class="form-control @error('harga_barang_pemasok') is-invalid @enderror"
                                    name="harga_barang_pemasok" value="{{ old('harga_barang_pemasok') }}" required
                                    oninput="funcSingkronTotalBayar()">
                                @error('harga_barang_pemasok')
                                    <span class="invalid-feedback" value="0" min="0" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="stok">Jumlah Stok:</label>
                                <input type="number" class="form-control" name="stok" id="stok"
                                    placeholder="Jumlah Stok" oninput="funcSingkronTotalBayar()" value="0"
                                    min="0" required>
                            </div>
                            <div class="form-group">
                                <label for="statusPembayaran">Status Pembayaran:</label>
                                <select class="form-control" name="status_pembelian" id="statusPembayaran"
                                    required="">

                                    <option value="lunas">Lunas</option>
                                    <option value="hutang">Hutang</option>
                                </select>
                            </div>
                            <div id="formCicilan" style="display: none;">
                                <div class="form-group">
                                    <label for="nominalTerbayar">Nominal Terbayar:</label>
                                    <input type="text" class="form-control" name="nominal_terbayar"
                                        id="nominalTerbayar" value="0">
                                </div>
                                <div class="form-group">
                                    <label for="tenggatBayar">Tenggat Waktu Bayar:</label>
                                    <input type="date" class="form-control" name="tenggat_bayar"
                                        id="tenggatBayar" value="2024-04-30">
                                </div>
                            </div>
                        </div>
                    </div>



                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="funcTambahUser()">Simpan</button>
            </div>
        </div>
    </div>
</div>
{{-- End of Modal Tambah User --}}

{{-- Modal Kurang Stok --}}
<div class="modal fade" id="KurangStokModal" tabindex="-1" role="dialog" aria-labelledby="KurangStokModal"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pengurangan Stok Barang</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('stok.minusstok.new') }}" id="formKurangStokModal" method="POST">

                    @csrf

                    <input type="hidden" name="id_barang" id="id_barangKurangStok" value="null">
                    <div class="form-group">
                        <label for="stok_referensi" class="form-label">Stok Sekarang</label>
                        <input type="text" class="form-control" id="stok_referensiKurangStok" readonly>
                    </div>
                    <div class="form-group">
                        <label for="stok_referensi" class="form-label">Stok Hasil</label>
                        <input type="text" class="form-control" id="stok_referensiHasilKurangStok" readonly>
                    </div>
                    <div class="form-group">
                        <label for="stok_kurang" class="form-label">Pengurangan Stok</label>
                        <input type="number" class="form-control" id="stok_kurangKurangStok" name="stok_kurang"
                            min="0" oninput="updateStokKurang()" value="0">
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="funcKurangStokSubmit()">Simpan</button>
            </div>
        </div>
    </div>
</div>
{{-- End of Modal Kurang Stok --}}

{{-- Modal Tambah Stok --}}
<div class="modal fade" id="tambahStokModal" tabindex="-1" role="dialog" aria-labelledby="tambahStokModal"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Stok Barang</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('stok.addstok.new') }}" id="formtambahStokModal" method="POST">

                    @csrf

                    <input type="hidden" name="id_barang" id="id_barangTambahStok" value="null">
                    <div class="form-group">
                        <label for="stok_referensi" class="form-label">Stok Referensi</label>
                        <input type="text" class="form-control" id="stok_referensiTambahStok" readonly>
                    </div>
                    <div class="form-group">
                        <label for="stok_referensi" class="form-label">Stok Hasil</label>
                        <input type="text" class="form-control" id="stok_referensiHasil" readonly>
                    </div>
                    <div class="form-group">
                        <label for="stok_tambah" class="form-label">Stok Tambah</label>
                        <input type="number" class="form-control" id="stok_tambahTambahStok" name="stok_tambah"
                            min="0" oninput="updateStokTambah()" value="0">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="funcTambahStokSubmit()">Simpan</button>
            </div>
        </div>
    </div>
</div>
{{-- End of Modal Tambah Stok --}}

{{-- Modal Edit --}}
<div class="modal fade" id="EditUser" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Barang</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="funcUpdateUser()">Simpan</button>
            </div>
        </div>
    </div>
</div>
{{-- End of Modal Edit --}}

{{-- Modal Delete --}}
<div class="modal fade" id="HapusUser" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Hapus Barang</h5>
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




<!-- Modal Edit Stok -->
<div class="modal fade" id="modalStok" tabindex="-1" role="dialog" aria-labelledby="modalStokLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="modalReturLabel">Edit Stok</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Formulir Pembayaran -->
                <form>
                    <div class="form-group">
                        <label for="pemasok">Pemasok:</label>
                        <input type="text" class="form-control" id="pemasok" placeholder="pemasok" required>
                    </div>
                    <div class="form-group">
                        <label for="nama_barang">Nama Barang:</label>
                        <input type="text" class="form-control" name="nama_barang" id="nama_barang"
                            placeholder="Nama Barang" required>
                    </div>
                    <div class="form-group">
                        <label for="ukuran">Ukuran Barang:</label>
                        <input type="text" class="form-control" name="ukuran" id="ukuran"
                            placeholder="Ukuran Barang" required>
                    </div>
                    <div class="form-group">
                        <label for="harga_barang">Harga Barang:</label>
                        <input type="number" class="form-control" name="harga_barang" id="harga_barang"
                            placeholder="Harga Barang" required>
                    </div>
                    <div class="form-group">
                        <label for="stok">Jumlah Stok:</label>
                        <input type="number" class="form-control" name="stok" id="stok"
                            placeholder="Jumlah Stok" required>
                    </div>
                </form>
                <button type="submit" class="btn btn-primary">Edit</button>
            </div>
        </div>
    </div>
</div>
@section('javascript-custom')
    <script></script>
    <script>
        function funcSingkronTotalBayar() {
            // // Harga Barang Pemasok
            // const hargaBarangPemasok = document.querySelector('#harga_barang_pemasok');
            // const jumlahStok = document.querySelector('#stok');
            // console.log(parseInt(hargaBarangPemasok.value), parseInt(jumlahStok.value))

            // // Total Terbayar
            // var formCicilan = document.getElementById('formCicilan');
            // const nominalTerbayar = formCicilan.querySelector('#nominalTerbayar');

            // nominalTerbayar.value = parseInt(hargaBarangPemasok.value) * parseInt(jumlahStok.value);


            // Element Select

            const typePembelian = document.querySelector('#statusPembayaran');
            var formCicilan = document.getElementById('formCicilan');
            if (typePembelian.value === 'hutang') {
                formCicilan.style.display = 'block';

                const nominalTerbayar = formCicilan.querySelector('#nominalTerbayar');
                nominalTerbayar.removeAttribute('readonly');
                nominalTerbayar.value = 0;
                const tanggalTenggatBayar = formCicilan.querySelector('#tenggatBayar');
                tanggalTenggatBayar.removeAttribute('disabled');
            } else {
                formCicilan.style.display = 'none';

                const nominalTerbayar = formCicilan.querySelector('#nominalTerbayar');
                nominalTerbayar.readOnly = true;
                const hargaBarangPemasok = document.querySelector('#harga_barang_pemasok');
                const jumlahStok = document.querySelector('#stok');
                console.log(parseInt(hargaBarangPemasok.value), parseInt(jumlahStok.value))
                nominalTerbayar.value = parseInt(hargaBarangPemasok.value) * parseInt(jumlahStok.value);

                const tanggalTenggatBayar = formCicilan.querySelector('#tenggatBayar');
                tanggalTenggatBayar.disabled = true;
            }
        }

        function funcTambahStokSubmit() {
            var form = document.querySelector('#formtambahStokModal');
            console.log(form);
            if (form.length > 0) {
                form.submit();
            } else {
                console.error('Form not found!');
            }


        }

        function funcKurangStokSubmit() {
            var form = document.querySelector('#formKurangStokModal');

            if (form.length > 0) {
                form.submit();


            } else {
                console.error('Form not found!');
            }
            return;
        }

        function updateStokTambah() {
            var stokReferensiHasil = $('#stok_referensiHasil');
            var stokTambah = parseInt($('#stok_tambahTambahStok').val());

            if (!isNaN(stokTambah)) {


                let maxReferensi = parseInt(stokReferensiHasil.attr('max'));

                $('#stok_referensiHasil').val(maxReferensi + stokTambah);
            }
        }

        function updateStokKurang() {
            var stokReferensiHasil = $('#stok_referensiHasilKurangStok');
            var stokKurang = $('#stok_kurangKurangStok');

            if (!isNaN(stokKurang.val())) {


                let maxReferensi = parseInt(stokReferensiHasil.attr('max'));
                if ((maxReferensi - stokKurang.val()) <= 0) {


                    stokKurang.val(maxReferensi);
                    $('#stok_referensiHasilKurangStok').val(0);


                } else {
                    $('#stok_referensiHasilKurangStok').val(maxReferensi - stokKurang.val());

                }
            } else if (stokKurang == 0 || stokKurang < 0) {

            }
        }
    </script>
    <script>
        document.getElementById('statusPembayaran').addEventListener('change', function() {
            funcSingkronTotalBayar();
        });
    </script>
    <script>
        // let table = new DataTable('#stokbarang', {
        //     responsive: true
        // });

        function funcTambahStok(url) {
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    console.log(response);
                    $('#id_barangTambahStok').val(response.data.hash_id_barang);
                    // Mengisi nilai modal dengan data yang diterima
                    $('#stok_referensiTambahStok').val(response.data.stok);
                    $('#stok_referensiHasil').val(response.data.stok);
                    $('#stok_referensiHasil').attr('max', response.data.stok); // Set nilai maksimum
                    $('#tambahStokModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });

        }

        function funcKurangStok(url) {
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    console.log(response);
                    $('#id_barangKurangStok').val(response.data.hash_id_barang);
                    // Mengisi nilai modal dengan data yang diterima
                    $('#stok_referensiKurangStok').val(response.data.stok);
                    $('#stok_referensiHasilKurangStok').val(response.data.stok);
                    $('#stok_referensiHasilKurangStok').attr('max', response.data.stok); // Set nilai maksimum
                    $('#KurangStokModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
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
