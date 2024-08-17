<form
    action="{{ route('cicilan.hutang.update', ['id_hutang_dan_stok' => $dataRiwayatHutang->hutang_dan_stok_id, 'id_riwayathutang' => $dataRiwayatHutang->id]) }}"
    id="formEditUser" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="nominal">Besaran:</label>
        <input type="number" name="nominal" id="nominal" class="form-control" value="{{ $dataRiwayatHutang->nominal_dibayar }}"
            step="any" value="">
    </div>



</form>
