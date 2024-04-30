<form action="{{ route('cicilan.hutang.update', ['id_bukubesar' => $dataBukuBesar->hash_id_bukubesar, 'id_barang' => $id_barang]) }}" id="formEditUser" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="nominal">Besaran:</label>
        <input type="number" name="nominal" id="nominal" class="form-control" value="{{ $dataBukuBesar->debit }}" step="any" value="">
    </div>



</form>
