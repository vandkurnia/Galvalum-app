<style>
    /* Sembunyikan Showing entries */
    #dataTable_length,
    #dataTable_info {
        display: none;
    }

    /* Sembunyikan pagination */
    #dataTable_paginate {
        display: none;
    }
</style>
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


    <div class="sidebar-heading">
        Master
    </div>
    <li class="nav-item {{ request()->is('user') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('/user') }}">
            <i class="fa fa-user" aria-hidden="true"></i>
            <span>Pengguna</span></a>
    </li>
    <li class="nav-item {{ request()->is('tipe-barang') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('/tipe-barang') }}">
            <i class="fa fa-archive" aria-hidden="true"></i>
            <span>Tipe Barang</span></a>
    </li>
    <li class="nav-item {{ request()->is('pemasok-barang') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('/pemasok-barang') }}">
            <i class="fa fa-user" aria-hidden="true"></i>
            <span>Pemasok Barang</span></a>
    </li>
    <li class="nav-item {{ request()->is('pembeli') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('/pembeli') }}">
            <i class="fa fa-user" aria-hidden="true"></i>
            <span>Pembeli</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">
    <!-- Nav Item - Penjualan -->
    <li class="nav-item {{ request()->is('beranda') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('/beranda') }}">
            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
            <span>Penjualan</span></a>
    </li>

    <!-- Nav Item - Retur -->
    <li class="nav-item {{ request()->is('retur') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('/retur') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Retur</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item {{ request()->is('stok') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="{{ url('/stok') }}">
            <i class="fa fa-archive" aria-hidden="true"></i>
            <span>Stok Barang</span>
        </a>
    </li>

    <li class="nav-item {{ request()->is('daftar_transaksi') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="{{ url('/daftar_transaksi') }}">
            <i class="fa fa-list" aria-hidden="true"></i>
            <span>Daftar Transaksi</span>
        </a>
    </li>

  
    <div class="sidebar-heading">
        Laporan
    </div>
    <li class="nav-item {{ request()->is('laporan/omzet') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="{{ route('laporan.omzet') }}">
            <i class="fa fa-book" aria-hidden="true"></i>
            <span>Laporan Omzet</span>
        </a>
    </li>
    <li class="nav-item {{ request()->is('laporan/hutang') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="{{ route('laporan.hutang') }}">
            <i class="fa fa-book" aria-hidden="true"></i>
            <span>Laporan Hutang</span>
        </a>
    </li>
    <li class="nav-item {{ request()->is('laporan/piutang') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="{{ route('laporan.piutang') }}">
            <i class="fa fa-book" aria-hidden="true"></i>
            <span>Laporan Piutang</span>
        </a>
    </li>
    <li class="nav-item {{ request()->is('laporan/kas-keluar') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="{{ route('laporan.kaskeluar') }}">
            <i class="fa fa-book" aria-hidden="true"></i>
            <span>Laporan Kas Keluar</span>
        </a>
    </li>
    <li class="nav-item {{ request()->is('laporan/modal-tambahan') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="{{ route('laporan.modaltambahan') }}">
            <i class="fa fa-book" aria-hidden="true"></i>
            <span>Laporan Modal Tambahan</span>
        </a>
    </li>
    <li class="nav-item {{ request()->is('laporan/laba-rugi') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="{{ route('laporan.labarugi') }}">
            <i class="fa fa-book" aria-hidden="true"></i>
            <span>Laporan Laba Rugi</span>
        </a>
    </li>
    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

</ul>
<!-- End of Sidebar -->
