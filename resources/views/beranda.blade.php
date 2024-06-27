@extends('app')

@section('title', 'Pemesanan')
@section('header-custom')
    {{-- <style>
        /* Sembunyikan Showing entries */
        #dataTable_length,
        #dataTable_info {
            display: none;
        }

        /* Sembunyikan pagination */
        #dataTable_paginate {
            display: none;
        }
    </style> --}}
    <script src="{{ secure_asset('library/ckeditor/ckeditor.js') }}"></script>

@endsection



@section('content')

    <script>
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};
    </script>
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

    </div>




    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Data Nota Pesanan</h6>
            </div>
            <div class="card-body">
                <form id="dataNota">
                    <div class="form-group">
                        <label for="NoNota">No Nota</label>
                        <input type="text" class="form-control" name="no_nota" id="NoNota" readonly
                            value="{{ $no_nota }}">
                    </div>

                    <div class="form-group d-flex flex-column">
                        <label for="namaPembeli">Nama Pembeli:</label>
                        {{-- <input type="text" class="form-control" id="namaPembeli" placeholder="Masukkan nama pembeli"
                    required> --}}
                        <select class="form-control" name="id_pembeli" id="namaPembeli" required>
                            <option value="">Pilih atau buat nama pembeli</option>
                        </select>

                    </div>
                    <div class="form-group">
                        <label for="noHp">Nomor HP:</label>
                        <input type="text" class="form-control" id="noHp" name="no_hp"
                            placeholder="Masukkan nomor HP" required>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat:</label>
                        <input type="text" class="form-control" id="alamat" name="alamat_pembeli"
                            placeholder="Masukkan alamat" required>
                    </div>

                    <div class="form-group">
                        <label for="jenisPelanggan">Jenis Pelanggan:</label>
                        <select class="form-control" name="jenis_pelanggan" id="jenisPelanggan" required>

                            <option value="harga_normal">Harga Normal</option>
                            <option value="aplicator">Aplicator</option>
                            <option value="potongan">Potongan</option>
                        </select>
                    </div>


                </form>
            </div>
        </div>
    </div>



    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Pesanan</h6>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col-md-5 mr-5">
                        <form id="pesanan" method="POST">
                            @csrf

                            <div class="form-group">

                                <label for="nama_barang">Nama Barang:</label>
                                <select class="form-control" id="nama_barang" name="nama_barang" required>

                                </select>
                            </div>
                            <div class="form-group">
                                <label for="jumlah_barang">Jumlah Barang:</label>
                                <input type="number" class="form-control" id="jumlah_barang" min="0" max="0"
                                    name="jumlah_barang" required>
                            </div>
                            <div class="form-group">
                                <label for="diskon">Diskon:</label>
                                <select class="form-control" id="diskon" name="diskon">
                                    <option value="" data-amount="0" data-type="amount">Normal</option>
                                    @foreach ($dataDiskon as $diskon)
                                        <option value="{{ $diskon->id_diskon }}" data-amount="{{ $diskon->besaran }}"
                                            data-type="{{ $diskon->type }}">{{ $diskon->nama_diskon }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="jenisPembelian">Jenis Pembelian:</label>
                                <select class="form-control" name="jenis_pembelian" id="jenis_pembelian" required>

                                    <option value="harga_normal">Harga Normal</option>
                                    <option value="aplicator">Aplicator</option>
                                    <option value="potongan">Potongan</option>
                                </select>
                            </div>
                            <div id="harga_khusus_input" class="form-group" style="display: none;">
                                <label for="harga_khusus">Harga Potongan Khusus:</label>
                                <input type="number" min="0" class="form-control" name="harga_khusus"
                                    id="harga_khusus" value="0">
                            </div>
                            <script>
                                document.getElementById('jenis_pembelian').addEventListener('change', function() {
                                    var hargaKhususInput = document.getElementById('harga_khusus_input');
                                    if (this.value === 'aplicator' || this.value === 'potongan') {
                                        hargaKhususInput.style.display = 'block';
                                        document.getElementById('harga_khusus').setAttribute('required', 'required');

                                    } else {
                                        hargaKhususInput.style.display = 'none';
                                        document.getElementById('harga_khusus').removeAttribute('required');
                                        document.getElementById('harga_khusus').value = 0;
                                    }
                                });
                            </script>
                            <button type="button" onclick="pemesananBarang()" class="btn btn-primary">Tambah
                                Pesanan</button>
                        </form>
                        <script>
                            function pemesananBarang() {
                                let barang = document.querySelector('#nama_barang');
                                let jumlah_barang = document.querySelector('#jumlah_barang');
                                // Check apakah barang dan jumlah_barang kosong, jika kosong maka ditolak alert()
                                if (barang.value === "" || jumlah_barang.value === "") {
                                    alert('Cannot submit blank value of barang or jumlah barang');
                                    return false;
                                }


                                // Ambil data barang dari session 
                                const data_barang = JSON.parse(sessionStorage.getItem('data_barang'))[0];

                                // Check apakah data input dari jumlah barang melebihi kapasitas total barang
                                let cek_total_barang_kelebihan = parseInt(data_barang.stok) < jumlah_barang.value ? true : false;

                                if (cek_total_barang_kelebihan) {
                                    let total_available_stok = data_barang.stok;
                                    alert(`Total barang cannot exceed available stock, total available : ${total_available_stok}`);

                                    // Ubah input ke available stok 
                                    jumlah_barang.value = total_available_stok;
                                    return false;
                                }
                                // Check jumlah barang tidak boleh minus
                                let cek_total_barang_kekurangan = jumlah_barang.value < 0 ? true : false;
                                if (cek_total_barang_kekurangan) {
                                    alert(`Total barang cannot less than 0`);

                                    // Ubah input ke available stok 
                                    jumlah_barang.value = 0;
                                    return false;
                                }


                                // Pembautan Table
                                let tbody_table = document.querySelector('#dataTablePesanan tbody');

                                // Hitung child pada tr untuk mengetahui totalnya berapa 
                                let total_tr = tbody_table.childElementCount;
                                if (total_tr === 0) {
                                    // Jika belum ada tr, set total_tr menjadi 1
                                    total_tr = 1;
                                } else {
                                    // Jika sudah ada tr, tambahkan 1 ke total_tr
                                    total_tr += 1;
                                }
                                // Buat pesanan barang
                                buatBarisPesananBarang(data_barang, total_tr, tbody_table);



                                // Reset Isi Form
                                // Mendapatkan elemen-elemen input dan select
                                // const nama_barang_input = document.getElementById('nama_barang');
                                const jumlah_barang_input = document.getElementById('jumlah_barang');

                                // Mengatur nilai input dan select kembali ke nilai awalnya
                                $('#nama_barang').val(null).trigger('change');
                                // nama_barang_input.value = ''; // Mengatur select kembali ke nilai awalnya
                                jumlah_barang_input.value = ''; // Mengatur input number kembali ke nilai awalnya

                                totalPembayaran();

                            }



                            function buatBarisPesananBarang(data_barang, total_tr, tbody_table) {


                                var tr_pesanan = tbody_table.querySelector(`tr[data-id-barang="${data_barang.hash_id_barang}"]`);
                                let check_id_tr_sudah_ada = tr_pesanan ? true :
                                    false;

                                if (!check_id_tr_sudah_ada) {
                                    var diskon_select_element = document.getElementById('diskon');
                                    var selected_diskon_element = diskon_select_element.options[diskon_select_element.selectedIndex];
                                    var amount = 0;
                                    var type = "amount";
                                    var harga_barang = Math.floor(data_barang.harga_barang);
                                    var harga_setelah_diskon = 0;
                                    let harga_diskon = 0;
                                    amount = selected_diskon_element.getAttribute('data-amount');
                                    type = selected_diskon_element.getAttribute('data-type');
                                    if (type === "percentage") {
                                        let jumlah_diskon = (harga_barang * amount) / 100
                                        harga_diskon = jumlah_diskon;
                                        harga_setelah_diskon = harga_barang - jumlah_diskon
                                    } else {
                                        harga_diskon = amount;
                                        harga_setelah_diskon = harga_barang - amount;
                                    }

                                    // Ambil Jenis Pembelian dan Harga Potongan Khususnya
                                    const jenisPelangganElement = document.getElementById('jenis_pembelian');
                                    const jenisPelangganTerpilih = jenisPelangganElement.options[jenisPelangganElement.selectedIndex].value;
                                    const hargaKhususInput = document.getElementById('harga_khusus');


                                    var tr_pesanan = document.createElement('tr');
                                    tr_pesanan.setAttribute('data-id-barang', data_barang.hash_id_barang);
                                    // Pembuatan TD
                                    let th_no = document.createElement('th');
                                    th_no.innerText = total_tr;
                                    let td_nama_barang = document.createElement('td');
                                    td_nama_barang.innerText = data_barang.nama_barang;
                                    let td_tipe_barang = document.createElement('td');
                                    td_tipe_barang.innerText = data_barang.tipe_barang;
                                    let td_ukuran_barang = document.createElement('td');
                                    td_ukuran_barang.innerText = data_barang.ukuran;
                                    let td_harga_barang = document.createElement('td');
                                    td_harga_barang.classList.add('harga_barang_pesanan');
                                    td_harga_barang.setAttribute('data-jenis-pelanggan', jenisPelangganTerpilih);
                                    td_harga_barang.setAttribute('data-harga-potongan-khusus', hargaKhususInput.value);
                                    td_harga_barang.innerText = harga_barang - parseInt(hargaKhususInput.value);
                                    let td_diskon = document.createElement('td');
                                    td_diskon.classList.add('diskon_pesanan')
                                    td_diskon.innerText = harga_diskon;
                                    let td_jumlah = document.createElement('td');
                                    td_jumlah.classList.add('nilai_jumlah_barang_pesanan');
                                    td_jumlah.innerText = jumlah_barang.value;

                                    let td_total = document.createElement('td');
                                    td_total.classList.add('totalperpesanan');


                                    let total = (harga_setelah_diskon - parseInt(hargaKhususInput.value)) * jumlah_barang.value;
                                    // Round up to 2 decimal places
                                    total = Math.ceil(total * 100) / 100;
                                    td_total.innerText = total.toFixed(0); // Display with comma separators if needed


                                    // Membuat tombol Edit
                                    // const edit_button = document.createElement('button');
                                    // edit_button.href = '#';
                                    // edit_button.classList.add('btn', 'btn-primary', 'btn-sm');
                                    // edit_button.innerHTML = '<i class="fas fa-edit"></i> Edit';
                                    // edit_button.onclick = function() {
                                    //     editPesananBarang(data_barang.hash_id_barang);
                                    // }

                                    // Membuat tombol Delete
                                    const delete_button = document.createElement('button');
                                    // delete_button.href = '#';
                                    delete_button.classList.add('btn', 'btn-danger', 'btn-sm', 'ml-2');
                                    delete_button.innerHTML = '<i class="fas fa-trash"></i> Delete';
                                    // Onclick untuk menghapus element ini
                                    delete_button.onclick = function() {
                                        hapusPesananBarang(data_barang.hash_id_barang);
                                    }

                                    // Membuat elemen td untuk menyimpan tombol-tombol
                                    let td_aksi = document.createElement('td');
                                    // td_aksi.appendChild(edit_button);
                                    td_aksi.appendChild(delete_button);


                                    // Append ke TR
                                    tr_pesanan.appendChild(th_no);
                                    tr_pesanan.appendChild(td_nama_barang);
                                    tr_pesanan.appendChild(td_tipe_barang);
                                    tr_pesanan.appendChild(td_ukuran_barang);
                                    tr_pesanan.appendChild(td_harga_barang);
                                    tr_pesanan.appendChild(td_diskon);
                                    tr_pesanan.appendChild(td_jumlah);
                                    tr_pesanan.appendChild(td_total);
                                    tr_pesanan.appendChild(td_aksi);


                                    // Append ke TBODY table
                                    tbody_table.appendChild(tr_pesanan);




                                    // Append ke TBODY table
                                    tbody_table.appendChild(tr_pesanan);



                                } else {

                                    // Ambil Jenis Pembelian dan Harga Potongan Khususnya
                                    const jenisPelangganElement = document.getElementById('jenis_pembelian');
                                    const jenisPelangganTerpilih = jenisPelangganElement.options[jenisPelangganElement.selectedIndex].value;
                                    const hargaKhususInput = document.getElementById('harga_khusus');
                                    // Diskon
                                    var diskon_select_element = document.getElementById('diskon');
                                    var selected_diskon_element = diskon_select_element.options[diskon_select_element.selectedIndex];

                                    var amount = 0;
                                    var type = "amount";
                                    var harga_barang = Math.floor(data_barang.harga_barang);
                                    var harga_setelah_diskon = 0;
                                    let harga_diskon = 0;
                                    amount = selected_diskon_element.getAttribute('data-amount');
                                    type = selected_diskon_element.getAttribute('data-type');
                                    if (type === "percentage") {
                                        let jumlah_diskon = (harga_barang * amount) / 100
                                        harga_diskon = jumlah_diskon;
                                        harga_setelah_diskon = harga_barang - jumlah_diskon
                                    } else {
                                        harga_diskon = amount;
                                        harga_setelah_diskon = harga_barang - amount;
                                    }

                                    const harga = tr_pesanan.querySelector('.harga_barang_pesanan');
                                    harga.innerText = harga_barang - parseInt(hargaKhususInput.value);
                                    harga.setAttribute('data-jenis-pelanggan', jenisPelangganTerpilih);
                                    harga.setAttribute('data-harga-potongan-khusus', hargaKhususInput.value);


                                    let diskon = tr_pesanan.querySelector('.diskon_pesanan');
                                    diskon.innerText = harga_diskon;
                                    let td_jumlah = tr_pesanan.querySelector('.nilai_jumlah_barang_pesanan');
                                    td_jumlah.innerText = jumlah_barang.value;

                                    let td_total = tr_pesanan.querySelector('.totalperpesanan');
                                    td_total.innerText = (harga_setelah_diskon - parseInt(hargaKhususInput.value)) * jumlah_barang.value;
                                }


                            }

                            function editPesananBarang(id_barang) {
                                const tbody_table = document.querySelector('#dataTablePesanan tbody');
                                const tr_element_select = tbody_table.querySelector(`tr[data-id-barang="` + id_barang + `"]`);
                                // Edit element nama barang
                                $('#nama_barang').val(id_barang); // Select the option with a value of '1'
                                $('#nama_barang').trigger('change'); // Notify any JS components that the value changed

                                // Ambil nilai dari td ke 6
                                const td_element_jumlah = tr_element_select.querySelector('.nilai_jumlah_barang_pesanan');

                                let nilai_jumlah = td_element_jumlah.innerText;

                                // Inisiasi dan Edit element jumlah barang 
                                const jumlah_barang_input = document.getElementById('jumlah_barang');
                                jumlah_barang_input.value = nilai_jumlah;

                                resetNoPesananBarang();
                            }

                            function hapusPesananBarang(id_barang) {
                                // Element TBODY Table
                                const tbody_table = document.querySelector('#dataTablePesanan tbody');
                                const element_to_remove = tbody_table.querySelector(`tr[data-id-barang="` + id_barang + `"]`);
                                // Hapus element
                                element_to_remove.remove();
                                resetNoPesananBarang();

                            }

                            function resetNoPesananBarang() {
                                totalPembayaran();

                                const tbody_table = document.querySelector('#dataTablePesanan tbody');

                                // Reset number pada no tr yang tersedia
                                let total_tr = tbody_table.childElementCount;
                                // ;
                                const all_th_number = tbody_table.querySelectorAll('tr th');
                                // atur number awal 1
                                var nilaiawal = 1;
                                for (const th_number of all_th_number) {
                                    th_number.innerText = nilaiawal;
                                    nilaiawal++;
                                }
                            }
                        </script>

                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTablePesanan" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Tipe Barang</th>
                                <th>Ukuran Barang</th>
                                <th>Harga Barang</th>
                                {{-- <th>Jenis Pelanggan</th> Nanti di uncomment --}}
                                <th>Diskon</th>
                                <th>Qty</th>
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" rowspan="5">

                                </td>
                                <td colspan="2">Sub Total Rp</td>
                                <td colspan="2"><input type="number" class="form-control" name="sub_total"
                                        id="subTotal" value="0" readonly></td>
                            </tr>
                            <tr>
                                <td colspan="2">Diskon Rp</td>
                                <td colspan="2"><input type="number" oninput="totalPembayaran()"
                                        class="form-control" name="diskon_total" id="diskonTotal" value="0"></td>
                            </tr>
                            <tr>
                                <td colspan="2">Ongkir</td>
                                <td colspan="2"><input oninput="totalPembayaran()" type="number"
                                        class="form-control" name="total_ongkir" min="0" id="totalOngkir"
                                        value="0"></td>
                            </tr>
                            <tr>
                                <td colspan="2">Dp</td>
                                <td colspan="2"><input oninput="totalPembayaran()" type="number"
                                        class="form-control" name="dp" min="0" id="nilaiDp"
                                        value="0"></td>
                            </tr>
                            <tr>
                                <td colspan="2"><strong>Total Rp</strong></td>
                                <td colspan="2"><strong><input type="number" class="form-control" name="total"
                                            id="total" value="0" readonly></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                {{-- <div class="mt-2">Total Pembayaran: <span id="total_pembayaran">Rp. 0</span></div> --}}
                <button type="submit" class="btn btn-primary mt-4 float-right" data-toggle="modal"
                    data-target="#modalBayar">Bayar</button>
            </div>
        </div>
    </div>
@endsection
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Modal Pembayaran -->
<div class="modal fade" id="modalBayar" tabindex="-1" role="dialog" aria-labelledby="modalBayarLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalBayarLabel">Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Formulir Pembayaran -->
                <form action="{{ route('pemesanan.store') }}" id="formPembeli" method="POST">
                    @csrf


                    <div class="form-group" id="jumlahBayarGroup" style="display: none;">
                        <label for="jumlahBayar">Jumlah Bayar:</label>
                        <input type="text" class="form-control" name="jumlah_bayar" id="jumlahBayar"
                            placeholder="Masukkan jumlah bayar">
                    </div>
                    <div class="form-group">
                        <label for="metodePembayaran">Metode Pembayaran</label>
                        <select name="metode_pembayaran" class="form-control" id="metodePembayaran">
                            <option value="CASH">CASH</option>
                            <option value="Transfer">Transfer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="statusPembayaran">Status Pembayaran:</label>
                        <select class="form-control" name="status_pembelian" id="statusPembayaran" required>

                            <option value="lunas">Lunas</option>
                            <option value="hutang">Hutang</option>
                        </select>
                    </div>

                    <div id="formCicilan" style="display: none;">
                        <div class="form-group">
                            <label for="nominalTerbayar">Nominal Terbayar:</label>
                            <input type="text" class="form-control" name="nominal_terbayar" id="nominalTerbayar"
                                value="0" readonly>
                        </div>
                        <div class="form-group">
                            <label for="tenggatBayar">Tenggat Waktu Bayar:</label>
                            <input type="date" class="form-control" name="tenggat_bayar" id="tenggatBayar"
                                value="{{ date('Y-m-d') }}" disabled>
                        </div>
                    </div>
                    {{-- <input type="hidden" name="pesanan[]" id="isiPesanan"> --}}
                    {{-- <input type="hidden" name="nota[]" id="dataNota"> --}}
                    <button type="button" onclick="kirimPesanan()" class="btn btn-primary">Bayar</button>
                </form>
            </div>
        </div>
    </div>
</div>



@section('javascript-custom')


    <script>
        document.getElementById('statusPembayaran').addEventListener('change', function() {
            var formCicilan = document.getElementById('formCicilan');
            let nilaiDp = document.getElementById('nilaiDp').value;
            if (this.value === 'hutang') {
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
                // nominalTerbayar.value = parseFloat(document.querySelector('#total').value) + parseFloat(nilaiDp);
                nominalTerbayar.value = parseFloat(document.querySelector('#total').value);

                const tanggalTenggatBayar = formCicilan.querySelector('#tenggatBayar');
                tanggalTenggatBayar.disabled = true;
            }
        });


        function totalPembayaran() {
            // validatetotalOngkir();
            // window.history.back(1);
            // Ambil semua harga barang dari tabel dan hitung totalnya
            var totalHarga = 0;
            // var totalDiskon = 0;
            $('#dataTablePesanan tbody tr').each(function(rows) {
                var harga = parseInt($(this).find('.harga_barang_pesanan').text())
                var jumlah = parseInt($(this).find('.nilai_jumlah_barang_pesanan').text());
                var diskon = parseInt($(this).find('.diskon_pesanan').text());
                // totalDiskon += diskon;
                var totalpesanan = parseInt($(this).find('.totalperpesanan').text());
                // totalHarga += (harga - diskon) * jumlah;
                totalHarga += totalpesanan;
                console.log(totalHarga);
            });


            var tabletfoot = document.querySelector('#dataTablePesanan tfoot');
            let sub_total = tabletfoot.querySelector('#subTotal');
            sub_total.value = totalHarga;
            let diskon = tabletfoot.querySelector('#diskonTotal');
            // diskon.value = totalDiskon;
            let ongkir = tabletfoot.querySelector('#totalOngkir');
            let total = tabletfoot.querySelector('#total');

            let nilaiTotal = parseInt(sub_total.value) - parseInt(diskon.value);
            // let nilaiPajak = nilaiTotal * (parseInt(pajak.value) / 100);
            let nilaiOngkir = parseInt(ongkir.value);


            // var nilaiDp = tabletfoot.querySelector('#nilaiDp');
            total.value = nilaiTotal + nilaiOngkir - parseFloat(nilaiDp.value);
            // total.value = nilaiTotal + nilaiOngkir;


            // Pengisian Total pada Nominal Terbayar
            document.querySelector('#nominalTerbayar').value = total.value;


            // Ubah ke format Rp dengan dipisah rupiah

            // Tampilkan total harga dalam elemen span
            // $('#total_pembayaran').text('Rp ' + totalHarga);


        }
    </script>

    <script>
        // Definisikan fungsi pencocokan matchStart
        function matchStart(params, data) {
            // Jika tidak ada istilah pencarian, kembalikan semua data
            if ($.trim(params.term) === '') {
                return data;
            }

            // Lewati jika tidak ada properti 'children'
            if (typeof data.children === 'undefined') {
                return null;
            }

            // `data.children` berisi opsi sebenarnya yang kita cocokkan
            var filteredChildren = [];
            $.each(data.children, function(idx, child) {
                if (child.text.toUpperCase().indexOf(params.term.toUpperCase()) === 0) {
                    filteredChildren.push(child);
                }
            });

            // Jika kita cocokkan salah satu dari anak-anak grup zona waktu, atur anak-anak yang cocok di grup
            // dan kembalikan objek grup
            if (filteredChildren.length) {
                var modifiedData = $.extend({}, data, true);
                modifiedData.children = filteredChildren;
                return modifiedData;
            }

            // Kembalikan `null` jika istilah tidak harus ditampilkan
            return null;
        }

        // Inisialisasi Select2 untuk input nama_barang
        // $(document).ready(function() {
        //     $(".js-example-matcher-start").select2({
        //         matcher: matchStart
        //     });
        // });
        // In your Javascript (external .js resource or <script> tag)


        function kirimPesanan() {
            const formPembeli = document.querySelector('#formPembeli');

            // Ambil data Nota lalu simpan ke data Pembeli
            document.querySelector('#dataNota').querySelectorAll('input, select').forEach(function(element) {

                // Buat elemen input hidden
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = element.getAttribute('name');
                input.value = element.value;
                // Tambahkan input ke formulir pembeli
                formPembeli.appendChild(input);
            });

            // Inisiasi data pesanan
            const dataPesanan = {
                pesanan: []
            };
            // Ambil TR untuk iterasi
            let tr_pesanan = document.querySelectorAll('#dataTablePesanan tbody tr');
            Array.from(tr_pesanan).forEach(function(tr) {
                const itemPesanan = {
                    jumlah_pesanan: tr.querySelector('td.nilai_jumlah_barang_pesanan').innerText,
                    id_barang: tr.getAttribute('data-id-barang'),
                    id_diskon: tr.getAttribute('data-id-diskon'),
                    jenis_pelanggan: tr.querySelector('td.harga_barang_pesanan').getAttribute(
                        'data-jenis-pelanggan'),
                    harga_potongan: tr.querySelector('td.harga_barang_pesanan').getAttribute(
                        'data-harga-potongan-khusus'),

                }
                dataPesanan.pesanan.push(itemPesanan);
            });

            // Buat elemen input untuk menyimpan data pesanan sebagai JSON
            const inputPesanan = document.createElement('input');
            inputPesanan.type = 'hidden';
            inputPesanan.name = 'pesanan'; // Nama input
            inputPesanan.value = JSON.stringify(dataPesanan.pesanan); // Nilai input (data pesanan sebagai JSON)

            // Tambahkan input ke formulir
            formPembeli.appendChild(inputPesanan);



            // Menambahkan pajak ke form
            const totalOngkir = document.querySelector('#totalOngkir');
            const inputtotalOngkirHidden = document.createElement('input');
            inputtotalOngkirHidden.type = 'hidden';
            inputtotalOngkirHidden.name = 'total_ongkir'; // Menetapkan nama input ke 'totalOngkir'
            inputtotalOngkirHidden.value = totalOngkir
                .value; // Menetapkan nilai input ke nilai dari input dengan id 'totalOngkir'
            formPembeli.appendChild(inputtotalOngkirHidden); // Menambahkan input tersembunyi ke dalam form

            // Menambahkan pajak ke form
            const TotalDiskon = document.querySelector('#diskonTotal');
            const inputTotalDiskonHidden = document.createElement('input');
            inputTotalDiskonHidden.type = 'hidden';
            inputTotalDiskonHidden.name = 'diskon'; // Menetapkan nama input ke 'TotalDiskon'
            inputTotalDiskonHidden.value = TotalDiskon
                .value; // Menetapkan nilai input ke nilai dari input dengan id 'TotalDiskon'
            formPembeli.appendChild(inputTotalDiskonHidden); // Menambahkan input tersembunyi ke dalam form


            // Menambahkan Form DP
            const nilaiDp = document.querySelector('#nilaiDp');
            const inputTotalDp = document.createElement('input');
            inputTotalDp.type = 'hidden';
            inputTotalDp.name = 'dp';
            inputTotalDp.value = nilaiDp.value;
            formPembeli.appendChild(inputTotalDp);


            formPembeli.submit();
            // return true;
            // kirim ke pesanan
            // fetch(formPembeli.action, {
            //         method: 'POST',
            //         body: dataPembeli
            //     })
            //     .then(response => {
            //         if (response.ok) {
            //             alert("berhasil mengirim pesanan");
            //             // window.location.reload();
            //         } else {
            //             alert("Terjadi kesalahan, Error :" + response.status);
            //         }
            //         // window.location.href = "URL_BARU"; // Ganti URL_BARU dengan URL yang diinginkan
            //     })
            //     .catch(error => {
            //         alert("Terjadi kesalahan, Error :" + error.message);
            //     });


        }
    </script>



    <script>
        $('#nama_barang').select2({
            placeholder: 'Ketik nama barang',
            allowClear: true,
            ajax: {
                url: "{{ route('json.semuabarang') }}",
                dataType: 'json',
                delay: 100,
                data: function(params) {

                    return {
                        query: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function(data, params) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    params.page = params.page || 1;

                    return {
                        results: data.items.data,
                        pagination: {
                            more: (params.page * 1) < data.items.total
                        }
                    };
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Check if the error status is "unauthorized" (401)
                    if (jqXHR.status === 401) {
                        // Reload the page
                        window.location.reload(true);
                    }
                },
                cache: true
            },
            // placeholder: 'Search for a repository',
            minimumInputLength: 0,
            templateResult: formatBarang,
            templateSelection: formatBarangSelection
        });


        function formatBarang(barang) {
            if (barang.loading) {
                return barang.text;
            }

            var container = $(
                `<div class = "select2-search-barang" data-id="` + barang.id + `" >` + barang.text +
                `</div>`
            );

            return container;
        }

        function formatBarangSelection(barang) {
            // ;
            var jumlahStok = document.getElementById("jumlah_barang");
            jumlahStok.max = barang.stok; // Set a new value (replace 100 with your desired maximum)



            // Simpan ke session untuk digunakan pada pengisian beranda
            // Contoh data barang
            var hash_id_barang = barang.id || "";
            var nama_barang = barang.nama_barang || "";
            var harga_barang = barang.harga_barang || "";

            var tipe_barang = barang.tipe_barang ? barang.tipe_barang.nama_tipe || "" : "";

            var ukuran = barang.ukuran || "";
            var stok = barang.stok || "";

            // Mendapatkan array dari sessionStorage atau inisiasi array kosong jika belum ada
            var data_barang = [];

            // Menambahkan data barang ke dalam array
            data_barang.push({
                hash_id_barang,
                nama_barang,
                harga_barang,
                tipe_barang,
                ukuran,
                stok
            });




            // Menyimpan array kembali ke dalam sessionStorage
            sessionStorage.setItem('data_barang', JSON.stringify(data_barang));


            return barang.nama_barang || barang.text;
        }
    </script>


    {{-- Script untuk bayar --}}
    <script>
        $('#namaPembeli').select2({
            placeholder: 'Ketik nama pembeli',
            allowClear: true,
            tags: true,
            ajax: {
                url: "{{ route('json.semuapembeli') }}",
                dataType: 'json',
                delay: 100,
                data: function(params) {

                    return {
                        query: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function(data, params) {

                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    params.page = params.page || 1;

                    return {
                        results: data.items.data,
                        pagination: {
                            more: (params.page * 2) < data.items.total
                        }
                    };
                },
                cache: true
            },

            // placeholder: 'Search for a repository',
            // minimumInputLength: 0,
            templateResult: formatPembeli,
            templateSelection: formatPembeliSelection
        });


        function formatPembeli(pembeli) {
            if (pembeli.loading) {
                return pembeli.text;
            }

            var container = $(
                `<div class = "select2-search-pembeli" data-id="` + pembeli.id + `" >` + pembeli.text +
                `</div>`
            );

            return container;
        }

        function formatPembeliSelection(pembeli) {


            if (pembeli && pembeli.nama_pembeli !== undefined && pembeli.nama_pembeli !== null) {

                // Isi alamat
                const alamat = document.querySelector('#alamat');
                alamat.value = pembeli.alamat_pembeli;
                alamat.readOnly = true;

                const no_hp = document.querySelector('#noHp');
                no_hp.readOnly = true;
                no_hp.value = pembeli.no_hp_pembeli;


                const jenis_pelanggan = document.getElementById('jenisPelanggan');
                const options_jenis_pelanggan = jenis_pelanggan.options;
                const jenis_pembelian = document.getElementById('jenis_pembelian');
                const options_jenis_pembelian = jenis_pembelian.options;
                for (let i = 0; i < options_jenis_pelanggan.length; i++) {

                    if (options_jenis_pelanggan[i].value === pembeli.jenis_pembeli) {
                        options_jenis_pelanggan[i].selected = true;
                        options_jenis_pembelian[i].selected = true;
                        break; // Keluar dari loop setelah opsi terpilih ditandai
                    }
                }
                // Membuat dan melewatkan event 'change' setelah opsi terpilih diatur
                const changeEvent = new Event('change');
                jenis_pembelian.dispatchEvent(changeEvent);


                return pembeli.nama_pembeli || pembeli.text;
            } else {

                // Remove readOnly attribute from alamat input if it exists
                const alamat = document.querySelector('#alamat');
                if (alamat.hasAttribute('readonly')) {
                    alamat.removeAttribute('readonly');
                }

                // Remove readOnly attribute from noHp input if it exists
                const no_hp = document.querySelector('#noHp');
                if (no_hp.hasAttribute('readonly')) {
                    no_hp.removeAttribute('readonly');
                }


                return pembeli.nama_pembeli || pembeli.text;

            }



        }


        // ClassicEditor
        //     .create(document.querySelector('#editor'))
        //     .catch(error => {
        //         console.error(error);
        //     });
    </script>
@endsection
