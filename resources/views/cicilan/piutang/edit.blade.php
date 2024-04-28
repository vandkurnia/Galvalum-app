<form action="{{ route('cicilan.update', ['id_bukubesar' => $dataBukuBesar->id_bukubesar, 'id_nota' => $id_nota]) }}" id="formEditUser" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="nominal">Besaran:</label>
        <input type="number" name="nominal" id="nominal" class="form-control" value="{{ $dataBukuBesar->debit }}" step="any" value="">
    </div>



</form>
