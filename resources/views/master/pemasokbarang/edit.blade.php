<form action="{{ route('pemasokbarang.update', ['id' => $dataPemasokTerpilih['hash_id_pemasok']]) }}" id="formEditUser" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="nama_pemasok" class="form-label">Nama Pemasok</label>
        <input type="text" class="form-control" id="nama_pemasok_edit" name="nama_pemasok"
            value="{{ $dataPemasokTerpilih['nama_pemasok'] }}" required>
    </div>
    <div class="form-group">
        <label for="no_telp_pemasok" class="form-label">Nomor Telepon Pemasok</label>
        <input type="text" class="form-control" id="no_telp_pemasok_edit" name="no_telp_pemasok"
            value="{{ $dataPemasokTerpilih['no_telp_pemasok'] }}" required>
    </div>

    <div class="form-group">
        <label for="alamat_pemasok" class="form-label">Alamat Pemasok</label>
        <input type="email" class="form-control" id="alamat_pemasok_edit" name="alamat_pemasok"
            value="{{ $dataPemasokTerpilih['alamat_pemasok'] }}" required>
    </div>

</form>
