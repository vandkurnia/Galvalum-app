<form action="{{ route('user.changepassword', ['id_admin' => $datauser->hash_id_admin]) }}" id="formUpdatePassword" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label class="form-label" for="nama_user">Nama</label>
        <input type="text" class="form-control" name="nama_user" id="nama_user" disabled value="{{ $datauser->nama_admin }}">
    </div>

    <div class="form-group">
        <label for="password_lama" class="form-label">Password Lama</label>
        <input type="password" class="form-control" name="password_lama" id="password_lama" required>
    </div>


    <div class="form-group">
        <label for="password_baru" class="form-label">Password Baru</label>
        <input type="password" class="form-control" name="password_baru" id="password_baru" required>
    </div>



</form>
