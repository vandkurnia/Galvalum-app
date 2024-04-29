<form action="{{ route('stok.update', ['id' => $dataBarang['hash_id_barang']]) }}" id="formEditUser" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="pemasok">Pemasok:</label>
        <select class="form-control" id="pemasok" name="id_pemasok" required>
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
        <label for="harga_barang">Harga Jual:</label>
        <input type="number" class="form-control" name="harga_barang" id="harga_barang" placeholder="Harga Barang"
            value="{{(int) $dataBarang['harga_barang'] }}" required>
    </div>
    {{-- <div class="form-group">
        <label for="harga_barang_pemasok">Harga Barang Pemasok</label>
        <input id="harga_barang_pemasok" type="text" class="form-control @error('harga_barang_pemasok') is-invalid @enderror" name="harga_barang_pemasok" value="{{ (int) $dataBarang['harga_barang_pemasok'] }}" required>
        @error('harga_barang_pemasok')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div> --}}
    {{-- <div class="form-group">
        <label for="stok">Jumlah Stok:</label>
        <input type="number" class="form-control" name="stok" id="stok" placeholder="Jumlah Stok"
            value="{{ $dataBarang['stok'] }}" required>
    </div> --}}


</form>
