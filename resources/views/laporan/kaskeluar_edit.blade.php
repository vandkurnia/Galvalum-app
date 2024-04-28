
<form action="{{ route('laporan.updatekas', ['id' => $laporan_kas_keluar['id_bukubesar']]) }}" id="formEditUser" method="POST">

    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="keterangan" class="form-label">Deskripsi</label>
        <input type="text" class="form-control" id="keterangan" name="keterangan"
            value="{{ $laporan_kas_keluar['keterangan'] }}" required>
    </div>
    <div class="form-group">
        <label for="kredit" class="form-label">Jumlah Pengeluaran</label>
        <input type="text" class="form-control" id="kredit" name="kredit"
            value="{{ $laporan_kas_keluar['kredit'] }}" required>
    </div>
    <div class="form-group">
        <label for="tanggal" class="form-label">Tanggal</label>
        <input type="date" class="form-control" id="tanggal" name="tanggal"
            value="{{ $laporan_kas_keluar['tanggal'] }}" required>
    </div>
   

</form>