<form action="{{ route('diskon.update', ['id' => $dataDiskon['hash_id_diskon']]) }}" id="formEditUser" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="kode_diskon">Kode Diskon:</label>
        <input type="text" name="kode_diskon" id="kode_diskon" class="form-control" value="{{ $dataDiskon->kode_diskon }}">
    </div>
    <div class="form-group">
        <label for="nama_diskon">Nama Diskon:</label>
        <input type="text" name="nama_diskon" id="nama_diskon" class="form-control" value="{{ $dataDiskon->nama_diskon }}">
    </div>
    <div class="form-group">
        <label for="type">Type:</label>
        <select name="type" id="type" class="form-control">
            <option value="percentage" {{ $dataDiskon->type == 'percentage' ? 'selected' : '' }}>Percentage</option>
            <option value="amount" {{ $dataDiskon->type == 'amount' ? 'selected' : '' }}>Amount</option>
        </select>
    </div>
    <div class="form-group">
        <label for="besaran">Besaran:</label>
        <input type="text" name="besaran" id="besaran" class="form-control" value="{{ $dataDiskon->besaran }}">
    </div>
    <div class="form-group">
        <label for="status">Status:</label>
        <select name="status" id="status" class="form-control">
            <option value="AKTIF" {{ $dataDiskon->status == 'AKTIF' ? 'selected' : '' }}>AKTIF</option>
            <option value="NONAKTIF" {{ $dataDiskon->status == 'NONAKTIF' ? 'selected' : '' }}>NONAKTIF</option>
        </select>
    </div>

</form>
