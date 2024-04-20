<table class="table">
    <thead>
        <tr>
            <th scope="col">No</th>
            <th scope="col">Nama Barang</th>
            <th scope="col">Total Pesanan</th>
            <th scope="col">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataBarangNotaPembeli as $index => $pesanan)
            <tr>
                <th scope="row">{{ $index + 1 }}</th>
                <td>{{ $pesanan->Barang->nama_barang }}</td>
                <td>{{ $pesanan->jumlah_pembelian }}</td>
                <td>
                    <a href="{{ route('retur.pemasok.add', ['id_pesanan' => $pesanan->id_pesanan]) }}"
                        class="btn btn-info btn-sm p-2"><i class="fas fa-info-circle"></i>
                        Retur Pemasok</a>
                    <a href="{{ route('retur.pembeli.add', ['id_pesanan' => $pesanan->id_pesanan]) }}"
                        class="btn btn-info btn-sm p-2"><i class="fas fa-info-circle"></i>
                        Retur Pembeli</a>
                </td>
            </tr>
        @endforeach

    </tbody>
</table>
