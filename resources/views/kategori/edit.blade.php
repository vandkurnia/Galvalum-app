<form action="{{ route('kategori.update', ['id' => $dataKategori['id_kategori']]) }}" id="formEditUser" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="keterangan">Nama Kategori</label>
        <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" value="{{$dataKategori['nama_kategori']}}">
    </div>

</form>
