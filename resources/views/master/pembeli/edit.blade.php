<form action="{{ route('pembeli.update', ['id' => $dataPembeli['hash_id_pembeli']]) }}" id="formEditUser"
    method="POST">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="nama_pembeli" class="form-label">Nama Pembeli</label>
        <input type="text" class="form-control" id="nama_pembeli_edit" name="nama_pembeli"
            value="{{ $dataPembeli['nama_pembeli'] }}" required>
    </div>
    <div class="form-group">
        <label for="alamat_pembeli" class="form-label">Alamat Pembeli</label>
        <input type="text" class="form-control" id="alamat_pembeli_edit" name="alamat_pembeli"
            value="{{ $dataPembeli['alamat_pembeli'] }}" required>
    </div>
    <div class="form-group">
        <label for="no_hp_pembeli" class="form-label">No HP Pembeli</label>
        <input type="text" class="form-control" id="no_hp_pembeli_edit" name="no_hp_pembeli"
            value="{{ $dataPembeli['no_hp_pembeli'] }}" required>
    </div>


</form>
