<div class="sidebar">
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-item">
                <a href="{{ url('/dashboard') }}" class="nav-link {{ $activeMenu == 'dashboard' ? 'active' : '' }} ">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>Dashboard</p>
                </a>
            </li>
            <li class="nav-header">Data Pengguna</li>
            {{-- <li class="nav-item">
                <a href="{{ url('/level') }}" class="nav-link {{ $activeMenu == 'level' ? 'active' : '' }} ">
                    <i class="nav-icon fas fa-layer-group"></i>
                    <p>Level User</p>
                </a>
            </li> --}}
            <li class="nav-item">
                <a href="{{ url('/admin') }}" class="nav-link {{ $activeMenu == 'admin' ? 'active' : '' }}">
                    <i class="nav-icon far fa-user"></i>
                    <p>Data Admin</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ url('/customer') }}" class="nav-link {{ $activeMenu == 'customer' ? 'active' : '' }}">
                    <i class="nav-icon far fa-user"></i>
                    <p>Data Customer</p>
                </a>
            </li>
            <li class="nav-header">Data Produk</li>
            <li class="nav-item">
                <a href="{{ url('/kategori') }}" class="nav-link {{ $activeMenu == 'kategori' ? 'active' : '' }} ">
                    <i class="nav-icon far fa-bookmark"></i>
                    <p>Kategori Produk</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ url('/produk') }}" class="nav-link {{ $activeMenu == 'barang' ? 'active' : '' }} ">
                    <i class="nav-icon far fa-list-alt"></i>
                    <p>Data Produk</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ url('/supplier') }}" class="nav-link {{ $activeMenu == 'supplier' ? 'active' : '' }}">
                    <i class="nav-icon far fa-user"></i>
                    <p>Data Supplier</p>
                </a>
            </li>
            <li class="nav-header">Data Transaksi</li>
            <li class="nav-item">
                <a href="{{ url('/stok') }}" class="nav-link {{ $activeMenu == 'stok' ? 'active' : '' }} ">
                    <i class="nav-icon fas fa-cubes"></i>
                    <p>Stok Pembelanjaan</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ url('/pesanan') }}" class="nav-link {{ $activeMenu == 'pesanan' ? 'active' : '' }} ">
                    <i class="nav-icon fas fa-clipboard-check"></i>
                    <p>Pesanan</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ url('/penjualan') }}" class="nav-link {{ $activeMenu == 'penjualan' ? 'active' : '' }} ">
                    <i class="nav-icon fas fa-cash-register"></i>
                    <p>Transaksi Penjualan</p>
                </a>
            </li>
            <li class="nav-header"></li>
            <li class="nav-item">
                <a href="{{ url('/logout') }}" class="nav-link btn btn-danger text-white">
                    <i class="nav-icon fas fa-sign-out-alt"></i>
                    <p>Logout</p>
                </a>
            </li>
        </ul>
    </nav>
</div>
