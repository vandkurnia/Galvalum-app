@extends('app')

@section('title', 'Stok barang')
@section('header-custom')



@endsection

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Stok Barang</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Pemasok</th>
                                <th>Nama Barang</th>
                                <th>Tipe Barang</th>
                                <th>Ukuran Barang</th>
                                <th>Harga Barang</th>
                                <th>Jumlah Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>No</th>
                                <th>Pemasok</th>
                                <th>Nama Barang</th>
                                <th>Tipe Barang</th>
                                <th>Ukuran Barang</th>
                                <th>Harga Barang</th>
                                <th>Jumlah Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <tr>
                                <th>1</th>
                                <td>Pemasok A</td>
                                <td>Galvalum Sheet 0.3mm</td>
                                <td>Sheet</td>
                                <td>0.3mm</td>
                                <td>150000</td>
                                <td>100</td>
                                <td>
                                    <a>
                                        <button type="submit" class="btn btn-primary btn-sm" data-toggle="modal"
                                            data-target="#modalStok">
                                            <span><i class="fas fa-edit"></i></span>Edit</button>
                                    </a>
                                    <a href="#" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                            <tr>
                                <th>2</th>
                                <td>Pemasok B</td>
                                <td>Galvalum Coil 0.5mm</td>
                                <td>Coil</td>
                                <td>0.5mm</td>
                                <td>200000</td>
                                <td>80</td>
                                <td>
                                    <a>
                                        <button type="submit" class="btn btn-primary btn-sm" data-toggle="modal"
                                            data-target="#modalStok">
                                            <span><i class="fas fa-edit"></i></span>Edit</button>
                                    </a>
                                    <a href="#" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                            <tr>
                                <th>3</th>
                                <td>Pemasok C</td>
                                <td>Galvalum Pipe 1 inch</td>
                                <td>Pipa</td>
                                <td>1 inch</td>
                                <td>180000</td>
                                <td>50</td>
                                <td>
                                    <a>
                                        <button type="submit" class="btn btn-primary btn-sm" data-toggle="modal"
                                            data-target="#modalStok">
                                            <span><i class="fas fa-edit"></i></span>Edit</button>
                                    </a>
                                    <a href="#" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                            <tr>
                                <th>4</th>
                                <td>Pemasok D</td>
                                <td>Galvalum Wire 2.5mm</td>
                                <td>Wire</td>
                                <td>2.5mm</td>
                                <td>220000</td>
                                <td>60</td>
                                <td>
                                    <a>
                                        <button type="submit" class="btn btn-primary btn-sm" data-toggle="modal"
                                            data-target="#modalStok">
                                            <span><i class="fas fa-edit"></i></span>Edit</button>
                                    </a>
                                    <a href="#" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                            <tr>
                                <th>5</th>
                                <td>Pemasok E</td>
                                <td>Galvalum Angle 40x40x3mm</td>
                                <td>Angle</td>
                                <td>40x40x3mm</td>
                                <td>190000</td>
                                <td>70</td>
                                <td>
                                    <a>
                                        <button type="submit" class="btn btn-primary btn-sm" data-toggle="modal"
                                            data-target="#modalStok">
                                            <span><i class="fas fa-edit"></i></span>Edit</button>
                                    </a>
                                    <a href="#" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                            <tr>
                                <th>6</th>
                                <td>Pemasok F</td>
                                <td>Galvalum Channel 50x25x2mm</td>
                                <td>Channel</td>
                                <td>50x25x2mm</td>
                                <td>210000</td>
                                <td>55</td>
                                <td>
                                    <a>
                                        <button type="submit" class="btn btn-primary btn-sm" data-toggle="modal"
                                            data-target="#modalStok">
                                            <span><i class="fas fa-edit"></i></span>Edit</button>
                                    </a>
                                    <a href="#" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                            <tr>
                                <th>7</th>
                                <td>Pemasok G</td>
                                <td>Galvalum Rod 6mm</td>
                                <td>Rod</td>
                                <td>6mm</td>
                                <td>230000</td>
                                <td>45</td>
                                <td>
                                    <a>
                                        <button type="submit" class="btn btn-primary btn-sm" data-toggle="modal"
                                            data-target="#modalStok">
                                            <span><i class="fas fa-edit"></i></span>Edit</button>
                                    </a>
                                    <a href="#" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                            <tr>
                                <th>8</th>
                                <td>Pemasok H</td>
                                <td>Galvalum Beam 100x50x5mm</td>
                                <td>Beam</td>
                                <td>100x50x5mm</td>
                                <td>250000</td>
                                <td>65</td>
                                <td>
                                    <a>
                                        <button type="submit" class="btn btn-primary btn-sm" data-toggle="modal"
                                            data-target="#modalStok">
                                            <span><i class="fas fa-edit"></i></span>Edit</button>
                                    </a>
                                    <a href="#" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                            <tr>
                                <th>9</th>
                                <td>Pemasok I</td>
                                <td>Galvalum Plate 2mm</td>
                                <td>Plate</td>
                                <td>2mm</td>
                                <td>280000</td>
                                <td>75</td>
                                <td>
                                    <a>
                                        <button type="submit" class="btn btn-primary btn-sm" data-toggle="modal"
                                            data-target="#modalStok">
                                            <span><i class="fas fa-edit"></i></span>Edit</button>
                                    </a>
                                    <a href="#" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                            <tr>
                                <th>10</th>
                                <td>Pemasok J</td>
                                <td>Galvalum Mesh 50x50x3mm</td>
                                <td>Mesh</td>
                                <td>50x50x3mm</td>
                                <td>270000</td>
                                <td>85</td>
                                <td>
                                    <a>
                                        <button type="submit" class="btn btn-primary btn-sm" data-toggle="modal"
                                            data-target="#modalStok">
                                            <span><i class="fas fa-edit"></i></span>Edit</button>
                                    </a>
                                    <a href="#" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</a>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


@endsection


<!-- Modal Edit Stok -->
<div class="modal fade" id="modalStok" tabindex="-1" role="dialog" aria-labelledby="modalStokLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="modalReturLabel">Edit Stok</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Formulir Pembayaran -->
                <form>
                    <div class="form-group">
                        <label for="pemasok">Pemasok:</label>
                        <input type="text" class="form-control" id="pemasok" placeholder="pemasok" required>
                    </div>
                    <div class="form-group">
                        <label for="nama_barang">Nama Barang:</label>
                        <input type="text" class="form-control" id="nama_barang" placeholder="nama_barang"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="ukuran_barang">Ukuran Barang:</label>
                        <input type="text" class="form-control" id="ukuran_barang" placeholder="ukuran_barang"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="harga_barang">Harga Barang:</label>
                        <input type="text" class="form-control" id="harga_barang" placeholder="harga_barang"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="jumlah_stok">Jumlah Stok:</label>
                        <input type="text" class="form-control" id="jumlah_stok" placeholder="jumlah_stok"
                            required>
                    </div>
                </form>
                <button type="submit" class="btn btn-primary">Edit</button>
            </div>
        </div>
    </div>
</div>
@section('javascript-custom')
@endsection
