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
                        <input type="text" class="form-control" id="no_retur_pembeli" name="no_retur_pembeli"
                            value="{{ $noReturPembeli }}" readonly required>
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
        <div class="card shadow mb-4">
            <div class="card-header py-4">
                <h6 class="m-0 font-weight-bold text-primary">Pesanan</h6>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col-md-5 mr-5">
                        <h2>Barang Pembelian</h2>
                        <form id="pesanan" method="POST">
                            @csrf

                            <div class="form-group">

                                <label for="nama_barang">Nama Barang:</label>
                                <select class="form-control" id="nama_barang" name="nama_barang" required>

                                </select>
                            </div>
                            <div class="form-group">
                                <label for="jumlah_barang">Jumlah Barang:</label>
                                <input type="number" class="form-control" id="jumlah_barang" min="0"
                                    max="0" name="jumlah_barang" required>
                            </div>
                            <div class="form-group">
                                <label for="diskon">Diskon:</label>
                                <select class="form-control" id="diskon" name="diskon">
                                    <option value="0" data-amount="0" data-type="amount">Normal</option>
                                    @foreach ($dataDiskon as $diskon)
                                        <option value="{{ $diskon->id_diskon }}" data-amount="{{ $diskon->besaran }}"
                                            data-type="{{ $diskon->type }}">{{ $diskon->nama_diskon }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button mb-4" onclick="pemesananBarang()" class="btn btn-primary">Tambah
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
                                let cek_total_barang_kelebihan = data_barang.stok < jumlah_barang.value ? true : false;;
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



                                    var tr_pesanan = document.createElement('tr');
                                    tr_pesanan.setAttribute('data-id-barang', data_barang.hash_id_barang);
                                    tr_pesanan.setAttribute('data-id-diskon', diskon_select_element.value);
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
                                    td_harga_barang.innerText = harga_barang;
                                    let td_diskon = document.createElement('td');
                                    td_diskon.classList.add('diskon_pesanan');
                                    td_diskon.innerText = harga_diskon;
                                    let td_jumlah = document.createElement('td');
                                    td_jumlah.classList.add('nilai_jumlah_barang_pesanan');
                                    td_jumlah.innerText = jumlah_barang.value;

                                    let td_total = document.createElement('td');
                                    td_total.classList.add('total');
                                    td_total.innerText = harga_setelah_diskon * jumlah_barang.value;


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


                                    // Kalau gagal update ke database maka hapus element
                                    updatePesanan(tr_pesanan);



                                    // Update Pesanan
                                    // End Update Pesanan

                                } else {

                                    // Update Pesanan
                                    updatePesanan(tr_pesanan);
                                    // End Update Pesanan
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
                                    let diskon = tr_pesanan.querySelector('.diskon_pesanan');
                                    diskon.innerText = harga_diskon;
                                    let td_jumlah = tr_pesanan.querySelector('.nilai_jumlah_barang_pesanan');
                                    td_jumlah.innerText = jumlah_barang.value;

                                    let td_total = tr_pesanan.querySelector('.total');
                                    td_total.innerText = harga_setelah_diskon * jumlah_barang.value;
                                }







                            }

                            function editPesananBarang(id_barang) {
                                const tbody_table = document.querySelector('#dataTablePesanan tbody');
                                const tr_element_select = tbody_table.querySelector(`tr[data-id-barang="` + id_barang + `"]`);
                                alert("id barang :" + id_barang);
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
                                const tr_to_remove = tbody_table.querySelector(`tr[data-id-barang="` + id_barang + `"]`);
                                // Hapus element
                                tr_to_remove.remove();
                                resetNoPesananBarang();


                                hapusPesanan(tr_to_remove);

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
                                <th>Qty Pesanan</th>
                                <th>Qty Retur</th>

                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 0;
                                $harga = 0;
                            @endphp
                            @foreach ($dataPesanan as $pesanan)
                                <tr data-id-barang="{{ $pesanan->Barang->hash_id_barang }}"
                                    data-id-pesanan="{{ $pesanan->id_pesanan }}">
                                    <th>{{ ++$no }}</th>
                                    <td>{{ $pesanan->Barang->nama_barang }}</td>
                                    <td>{{ $pesanan->Barang->TipeBarang->nama_tipe }}</td>
                                    <td>{{ $pesanan->Barang->ukuran }}</td>
                                    <td class="harga_barang_pesanan">{{ (int) $pesanan->harga }}</td>
                                    {{-- <td>Jenis Pelanggan</td> Nanti di uncomment --}}
                                    <td class="diskon_pesanan">{{ (int) $pesanan->diskon }}</td>
                                    <td class="nilai_jumlah_barang_pesanan">{{ (int) $pesanan->jumlah_pembelian }}</td>
                                    <td><input type="number" data-id-pesanan="{{ $pesanan->id_pesanan }}"
                                            class="" oninput="updateTotal(this)"
                                            max="{{ (int) $pesanan->jumlah_pembelian }}" class="form-control"
                                            value="0"></td>
                                    <td class="total">
                                        {{ (int) ($pesanan->harga - $pesanan->diskon) * $pesanan->jumlah_pembelian }}</td>
                                    <td>

                                        {{-- <button class="btn btn-primary btn-sm"
                                            onclick="editPesananBarang('{{ $pesanan->Barang->hash_id_barang }}')">
                                            <i class="fas fa-edit"></i> Edit
                                        </button> --}}
                                        <button class="btn btn-danger btn-sm ml-2"
                                            onclick="hapusPesananBarang('{{ $pesanan->Barang->hash_id_barang }}')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                                @php
                                    $harga += $pesanan->Barang->harga_barang * $pesanan->jumlah_pembelian;
                                @endphp
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" rowspan="4">

                                </td>
                                <td colspan="2">Sub Total Rp</td>
                                <td colspan="3"><input type="number" class="form-control" name="sub_total"
                                        id="subTotal" value="{{ $notaPembelian->sub_total }}" readonly>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">Diskon Rp</td>
                                <td colspan="3"><input type="number" class="form-control" name="diskon_total"
                                        id="diskonTotal" value="{{ $notaPembelian->diskon }}" readonly>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">Pajak Rp</td>
                                <td colspan="3"><input oninput="totalPembayaran()" type="number"
                                        class="form-control" name="total_pajak" id="totalPajak"
                                        value="{{ $notaPembelian->pajak }}"></td>
                            </tr>
                            <tr>
                                <td colspan="2"><strong>Total Rp</strong></td>
                                <td colspan="3"><strong><input type="number" class="form-control" name="total"
                                            id="total" value="{{ $notaPembelian->total }}" readonly></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                {{-- <div class="mt-2">Total Pembayaran: <span id="total_pembayaran">Rp. 0</span></div> --}}
                <button type="submit" class="btn btn-primary mt-4 float-right" data-toggle="modal"
                    data-target="#modalBayar">Bayar</button>
            </div>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Retur</h6>
            </div>
            <div class="card-body">
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
                                <td colspan="5" rowspan="4">

                                </td>
                                <td colspan="2">Sub Total Rp</td>
                                <td colspan="2"><input type="number" class="form-control" name="sub_total"
                                        id="subTotal" value="0" readonly></td>
                            </tr>
                            <tr>
                                <td colspan="2">Diskon Rp</td>
                                <td colspan="2"><input type="number" class="form-control" name="diskon_total"
                                        id="diskonTotal" value="0" readonly></td>
                            </tr>
                            <tr>
                                <td colspan="2">Pajak Rp</td>
                                <td colspan="2"><input oninput="totalPembayaran()" type="number"
                                        class="form-control" name="total_pajak" id="totalPajak" value="0"></td>
                            </tr>
                            <tr>
                                <td colspan="2"><strong>Total Rp</strong></td>
                                <td colspan="2"><strong><input type="number" class="form-control" name="total"
                                            id="total" value="0" readonly></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    </div>


@endsection







@section('javascript-custom')
    <script>
        function updateTotal(input) {
            let id_pesanan = input.getAttribute('data-id-pesanan');
            let tbody_table = document.querySelector('#dataTablePesanan tbody');
            var tr_pesanan = tbody_table.querySelector(`tr[data-id-pesanan="${id_pesanan}"]`);

            const qtyPesananInput = tr_pesanan.querySelector('.nilai_jumlah_barang_pesanan');
            const qtyReturInput = input;
            const qtyPesananValue = parseFloat(qtyPesananInput.innerText);


            const qtyReturValue = parseFloat(qtyReturInput.value);

            if (qtyReturValue <= 0) {
                qtyReturInput.value = 0;

            } else if (qtyReturValue > parseFloat(qtyReturInput.max)) {
                qtyReturInput.value = qtyReturInput.max;
            }



            let newQtyPesananValue = qtyReturInput.max - qtyReturValue;
            console.log(newQtyPesananValue);
            qtyPesananInput.innerText = newQtyPesananValue < 0 ? 0 : newQtyPesananValue;
        }
    </script>


    <script>
        function totalPembayaran() {
            // window.history.back(1);
            // Ambil semua harga barang dari tabel dan hitung totalnya
            var totalHarga = 0;
            var totalDiskon = 0;
            $('#dataTablePesanan tbody tr').each(function(rows) {
                var harga = parseInt($(this).find('.harga_barang_pesanan').text())
                var jumlah = parseInt($(this).find('.nilai_jumlah_barang_pesanan').text());
                var diskon = parseInt($(this).find('.diskon_pesanan').text());
                totalDiskon += diskon;
                totalHarga += (harga - diskon) * jumlah;
            });


            var tabletfoot = document.querySelector('#dataTablePesanan tfoot');
            let sub_total = tabletfoot.querySelector('#subTotal');
            sub_total.value = totalHarga;
            let diskon = tabletfoot.querySelector('#diskonTotal');
            diskon.value = totalDiskon;
            let pajak = tabletfoot.querySelector('#totalPajak');
            let total = tabletfoot.querySelector('#total');

            total.value = parseInt(sub_total.value) - parseInt(diskon.value) - parseInt(pajak.value);


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
            const totalPajak = document.querySelector('#totalPajak');
            const inputTotalPajakHidden = document.createElement('input');
            inputTotalPajakHidden.type = 'hidden';
            inputTotalPajakHidden.name = 'pajak'; // Menetapkan nama input ke 'totalPajak'
            inputTotalPajakHidden.value = totalPajak
                .value; // Menetapkan nilai input ke nilai dari input dengan id 'totalPajak'
            formPembeli.appendChild(inputTotalPajakHidden); // Menambahkan input tersembunyi ke dalam form




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
