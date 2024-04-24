<form action="{{ route('laporan.updateModal', ['id' => $laporan_modal_tambahan['id_modal_tambahan']]) }}" id="formEditUser" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="jenis_modal_tambahan" class="form-label">Jenis Modal Tambahan</label>
        <input type="text" class="form-control" id="jenis_modal_tambahan" name="jenis_modal_tambahan"
            value="{{ $laporan_modal_tambahan['jenis_modal_tambahan'] }}" required>
    </div>
    <div class="form-group">
        <label for="deskripsi" class="form-label">Deskripsi</label>
        <input type="text" class="form-control" id="deskripsi" name="deskripsi"
            value="{{ $laporan_modal_tambahan['deskripsi'] }}" required>
    </div>
    <div class="form-group">
        <label for="jumlah_modal" class="form-label">Jumlah Modal</label>
        <input type="text" class="form-control" id="jumlah_modal" name="jumlah_modal"
            value="{{ $laporan_modal_tambahan['jumlah_modal'] }}" required>
    </div>
    <div class="form-group">
        <label for="tanggal" class="form-label">Tanggal</label>
        <input type="date" class="form-control" id="tanggal" name="tanggal"
            value="{{ $laporan_modal_tambahan['tanggal'] }}" required>
    </div>
   

</form>