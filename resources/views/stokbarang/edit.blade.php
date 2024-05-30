<form action="{{ route('stok.update', ['id' => $dataBarang['hash_id_barang']]) }}" id="formEditUser" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="pemasok">Pemasok:</label>
                <select class="form-control" id="pemasok" name="id_pemasok" required>
                    <option value="">Tanpa Pemasok</option>
                    @foreach ($dataPemasok as $pemasok)
                        <option value="{{ $pemasok['id_pemasok'] }}"
                            {{ $dataBarang['id_pemasok'] == $pemasok['id_pemasok'] ? 'selected' : '' }}>
                            {{ $pemasok['nama_pemasok'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="kode_barang">Kode Barang</label>
                <input id="kode_barang" type="text" class="form-control @error('kode_barang') is-invalid @enderror"
                    name="kode_barang" value="{{ $dataBarang['kode_barang'] }}" required>
                @error('kode_barang')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="nama_barang">Nama Barang:</label>
                <input type="text" class="form-control" name="nama_barang" id="nama_barang" placeholder="Nama Barang"
                    value="{{ $dataBarang['nama_barang'] }}" required>
            </div>
            <div class="form-group">
                <label for="ukuran">Ukuran Barang:</label>
                <input type="text" class="form-control" name="ukuran" id="ukuran" placeholder="Ukuran Barang"
                    value="{{ $dataBarang['ukuran'] }}" required>
            </div>
            <div class="form-group">
                <label for="id_tipe_barang">Tipe Barang:</label>
                <select class="form-control" id="id_tipe_barang" name="id_tipe_barang" required>
                    @foreach ($dataTipeBarang as $tipeBarang)
                        <option value="{{ $tipeBarang['id_tipe_barang'] }}"
                            {{ $dataBarang['id_tipe_barang'] == $tipeBarang['id_tipe_barang'] ? 'selected' : '' }}>
                            {{ $tipeBarang['nama_tipe'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="stok">Stok:</label>
                <input type="number" class="form-control" stok-original="{{ $dataBarang->stokoriginal }}" stok-total="{{  $dataBarang->stok }}" name="stok" id="stok"
                    value="{{  $dataBarang->stok }}" oninput="calculateTotalNominalTerbayar()" required>
            </div>

        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="harga_barang">Harga Jual:</label>
                <input type="number" class="form-control" name="harga_barang" id="harga_barang"
                    placeholder="Harga Barang" value="{{ (int) $dataBarang['harga_barang'] }}" required>
            </div>
            <div class="form-group">
                <label for="harga_barang_pemasok">Harga Barang Pemasok</label>
                <input id="harga_barang_pemasok" type="text"
                    class="form-control @error('harga_barang_pemasok') is-invalid @enderror" min="0"
                    name="harga_barang_pemasok" oninput="calculateTotalNominalTerbayar()" value="{{ (int) $dataBarang->harga_barang_pemasok }}" required>
                @error('harga_barang_pemasok')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>



            <div class="form-group d-none" >
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="statusChangeCheckbox"
                        name="status_pembayaran_change">
                    <label class="form-check-label" for="statusChangeCheckbox">Status Pembayaran Berubah</label>
                </div>
            </div>


            <div class="form-group">
              
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>Perhatian!</strong> Merubah status pembelian akan mereset laporan hutang barang
                    ini.

                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <label for="statusPembayaranEdit">Status Pembayaran:</label>
                <select class="form-control" name="status_pembelian"
                    data-status-pembayaran="{{ $dataBarang->total == $dataBarang->nominal_terbayar ? 'lunas' : 'hutang' }}"
                    onchange="handleStatusPembayaranChange()" id="statusPembayaranEdit" required="">

                    <option {{ $dataBarang->total == $dataBarang->nominal_terbayar ? 'selected' : '' }} value="lunas">
                        Lunas</option>
                    <option {{ $dataBarang->total != $dataBarang->nominal_terbayar ? 'selected' : '' }} value="hutang">
                        Hutang</option>
                </select>
            </div>
            <div id="formCicilanEdit"
                style="{{ $dataBarang->total == $dataBarang->nominal_terbayar ? 'display: none;' : '' }}">
                <div class="form-group">
                    <label for="nominalTerbayar">DP :</label>
                    <input type="text" class="form-control" name="nominal_terbayar" id="nominalTerbayar"
                        value="{{ (int) $dataBarang->nominal_terbayar }}"
                        {{ $dataBarang->total == $dataBarang->nominal_terbayar ? 'readonly' : '' }}>
                </div>
                <div class="form-group">
                  
                    <label for="tenggatBayar">Tenggat Waktu Bayar:</label>
                    <input type="date" class="form-control" name="tenggat_bayar"
                        {{ $dataBarang->total == $dataBarang->nominal_terbayar ? 'disabled' : '' }} id="tenggatBayar"
                        value="{{ $dataBarang->tenggat_bayar ?? date('Y-m-d') }}">
                </div>
            </div>

        </div>
    </div>




</form>
