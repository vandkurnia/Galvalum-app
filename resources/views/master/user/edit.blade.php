<form action="{{ route('user.update', ['id' => $datauser['hash_id_admin']]) }}" id="formEditUser" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="nama_admin" class="form-label">Nama Admin</label>
        <input type="text" class="form-control" id="nama_admin_edit" name="nama_admin"
            value="{{ $datauser['nama_admin'] }}" required>
    </div>
    <div class="form-group">
        <label for="no_telp_admin" class="form-label">Nomor Telepon Admin</label>
        <input type="text" class="form-control" id="no_telp_admin_edit" name="no_telp_admin"
            value="{{ $datauser['no_telp_admin'] }}" required>
    </div>

    <div class="form-group">
        <label for="role" class="form-label">Role</label>
        <select name="role" id="role" class="form-control">
            <option value="admin" {{ $datauser['role'] == "admin" ? 'selected' : '' }}>Admin</option>
            <option value="karyawan" {{ $datauser['role'] == "karyawan" ? 'selected' : '' }}>Karyawan</option>
        </select>
    </div>

    <div class="form-group">
        <label for="email_admin" class="form-label">Alamat Email Admin</label>
        <input type="email" class="form-control" id="email_admin_edit" name="email_admin"
            value="{{ $datauser['email_admin'] }}" required>
    </div>

</form>
