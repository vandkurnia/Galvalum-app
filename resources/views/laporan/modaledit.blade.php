<form action="{{ route('laporan.updateModal', ['id' => $laporan_modal_tambahan['id_bukubesar']]) }}" id="formEditUser" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="keterangan" class="form-label">Deskripsi</label>
        <input type="text" class="form-control" id="keterangan" name="keterangan"
            value="{{ $laporan_modal_tambahan['keterangan'] }}" required>
    </div>
    <div class="form-group">
        <label for="debit" class="form-label">Jumlah Modal</label>
        <input type="text" class="form-control" id="debit" name="debit"
            value="{{ $laporan_modal_tambahan['debit'] }}" required>
    </div>
    <div class="form-group">
        <label for="tanggal" class="form-label">Tanggal</label>
        <input type="date" class="form-control" id="tanggal" name="tanggal"
            value="{{ $laporan_modal_tambahan['tanggal'] }}" required>
    </div>
   

</form>