<form
    action="{{ route('cicilan.hutang.update', ['id_bukubesar' => $dataRiwayatHutang->id, 'id_barang' => $id_barang]) }}"
    id="formEditUser" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="nominal">Besaran:</label>
        <input type="number" name="nominal" id="nominal" class="form-control" value="{{ $dataRiwayatHutang->nominal_dibayar }}"
            step="any" value="">
    </div>



</form>
