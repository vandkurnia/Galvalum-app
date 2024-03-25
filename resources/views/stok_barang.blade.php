@extends('app')
<div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

        <!-- Sidebar - Brand -->
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
            <div class="sidebar-brand-icon rotate-n-15">
                <i class="fas fa-laugh-wink"></i>
            </div>
            <div class="sidebar-brand-text mx-3">Galvalum App</div>
        </a>

        <!-- Divider -->
        <hr class="sidebar-divider my-0">

        <!-- Nav Item - Penjualan -->
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                <span>Penjualan</span></a>
        </li>

        <!-- Nav Item - Retur -->
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Retur</span></a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Nav Item - Pages Collapse Menu -->
        <li class="nav-item active">
            <a class="nav-link collapsed" href="#">
                <i class="material-symbols-outlined">
                    inventory_2</i>
                <span>Stok Barang</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="#">
                <i class="fa fa-list" aria-hidden="true"></i>
                <span>Daftar Transaksi</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="#">
                <i class="material-symbols-outlined fa-lg">
                    lab_profile</i>
                <span>Laporan</span>
            </a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider d-none d-md-block">

    </ul>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                <!-- Sidebar Toggle (Topbar) -->
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>

                <!-- Topbar Navbar -->
                <ul class="navbar-nav ml-auto">

                    <!-- Nav Item - Alerts -->
                    <li class="nav-item dropdown no-arrow mx-1">
                        <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-bell fa-fw"></i>
                            <!-- Counter - Alerts -->
                            <span class="badge badge-danger badge-counter">3+</span>
                        </a>
                        <!-- Dropdown - Alerts -->
                        <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                            aria-labelledby="alertsDropdown">
                            <h6 class="dropdown-header">
                                Alerts Center
                            </h6>
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                <div class="mr-3">
                                    <div class="icon-circle bg-primary">
                                        <i class="fas fa-file-alt text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="small text-gray-500">December 12, 2019</div>
                                    <span class="font-weight-bold">A new monthly report is ready to download!</span>
                                </div>
                            </a>
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                <div class="mr-3">
                                    <div class="icon-circle bg-success">
                                        <i class="fas fa-donate text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="small text-gray-500">December 7, 2019</div>
                                    $290.29 has been deposited into your account!
                                </div>
                            </a>
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                <div class="mr-3">
                                    <div class="icon-circle bg-warning">
                                        <i class="fas fa-exclamation-triangle text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="small text-gray-500">December 2, 2019</div>
                                    Spending Alert: We've noticed unusually high spending for your account.
                                </div>
                            </a>
                            <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
                        </div>
                    </li>

                    <div class="topbar-divider d-none d-sm-block"></div>

                    <!-- Nav Item - User Information -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">Douglas McGee</span>
                            <img class="img-profile rounded-circle"
                                src="{{ asset('assets/img/undraw_profile.svg')}} ">
                        </a>
                        <!-- Dropdown - User Information -->
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                            aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                Settings
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Logout
                            </a>
                        </div>
                    </li>

                </ul>

            </nav>
            <!-- End of Topbar -->

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
                                                <button type="submit" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalStok">
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
                                                <button type="submit" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalStok">
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
                                                <button type="submit" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalStok">
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
                                                <button type="submit" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalStok">
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
                                                <button type="submit" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalStok">
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
                                                <button type="submit" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalStok">
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
                                                <button type="submit" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalStok">
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
                                                <button type="submit" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalStok">
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
                                                <button type="submit" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalStok">
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
                                                <button type="submit" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalStok">
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

        </div>
        <!-- End of Main Content -->

        <!-- Modal Edit Stok -->
        <div class="modal fade" id="modalStok" tabindex="-1" role="dialog" aria-labelledby="modalStokLabel" aria-hidden="true">
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
                                <input type="text" class="form-control" id="nama_barang" placeholder="nama_barang" required>
                            </div>
                            <div class="form-group">
                                <label for="ukuran_barang">Ukuran Barang:</label>
                                <input type="text" class="form-control" id="ukuran_barang" placeholder="ukuran_barang" required>
                            </div>
                            <div class="form-group">
                                <label for="harga_barang">Harga Barang:</label>
                                <input type="text" class="form-control" id="harga_barang" placeholder="harga_barang" required>
                            </div>
                            <div class="form-group">
                                <label for="jumlah_stok">Jumlah Stok:</label>
                                <input type="text" class="form-control" id="jumlah_stok" placeholder="jumlah_stok" required>
                            </div>
                        </form>
                        <button type="submit" class="btn btn-primary">Edit</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="sticky-footer bg-white">
            <div class="container my-auto">
                <div class="copyright text-center my-auto">
                    <span>Copyright &copy; Your Website 2021</span>
                </div>
            </div>
        </footer>
        <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

</div>