<form action="{{ route('cicilan.update', ['id_piutang' => $riwayatPiutang->id_piutang, 'id_nota' => $id_nota]) }}" id="formEditUser" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="nominal">Besaran:</label>
        <input type="number" name="nominal" id="nominal" class="form-control" value="{{ $riwayatPiutang->nominal_dibayar }}" step="any" value="">
    </div>



</form>
