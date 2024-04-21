<form action="{{ route('bukubesar.update', ['id' => $dataBukuBesar['hash_id_bukubesar']]) }}" id="formEditUser" method="POST">
    @csrf
    @method('PUT')
   
   

    <div class="form-group">
        <label for="tanggal">Tanggal</label>
        <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{date('Y-m-d', strtotime($dataBukuBesar['tanggal']))}}">
    </div>

    <div class="form-group">
        <label for="kategori">Kategori</label>
        <input type="text" class="form-control" id="kategori" name="kategori" value="{{$dataBukuBesar['kategori']}}">
    </div>

    <div class="form-group">
        <label for="keterangan">Keterangan</label>
        <input type="text" class="form-control" id="keterangan" name="keterangan" value="{{$dataBukuBesar['keterangan']}}">
    </div>

</form>
