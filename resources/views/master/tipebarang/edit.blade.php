<form action="{{ route('tipebarang.update', ['id' => $dataTipeBarang['hash_id_tipe_barang']]) }}" id="formEditUser" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="nama_tipe" class="form-label">Nama Tipe</label>
        <input type="text" class="form-control" id="nama_tipe_edit" name="nama_tipe"
            value="{{ $dataTipeBarang['nama_tipe'] }}" required>
    </div>
   

</form>
