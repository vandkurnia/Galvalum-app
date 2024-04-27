
<form action="{{ route('laporan.updatekas', ['id' => $laporan_kas_keluar['id_kas_keluar']]) }}" id="formEditUser" method="POST">

    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="nama_pengeluaran" class="form-label">Nama Pengeluaran</label>
        <input type="text" class="form-control" id="nama_pengeluaran" name="nama_pengeluaran"
            value="{{ $laporan_kas_keluar['nama_pengeluaran'] }}" required>
    </div>
    <div class="form-group">
        <label for="deskripsi" class="form-label">Deskripsi</label>
        <input type="text" class="form-control" id="deskripsi" name="deskripsi"
            value="{{ $laporan_kas_keluar['deskripsi'] }}" required>
    </div>
    <div class="form-group">
        <label for="jumlah_pengeluaran" class="form-label">Jumlah Pengeluaran</label>
        <input type="text" class="form-control" id="jumlah_pengeluaran" name="jumlah_pengeluaran"
            value="{{ $laporan_kas_keluar['jumlah_pengeluaran'] }}" required>
    </div>
    <div class="form-group">
        <label for="tanggal" class="form-label">Tanggal</label>
        <input type="date" class="form-control" id="tanggal" name="tanggal"
            value="{{ $laporan_kas_keluar['tanggal'] }}" required>
    </div>
   

</form>