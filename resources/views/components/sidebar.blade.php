<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="index.html">Aset</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="index.html">St</a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">Dashboard</li>
            <li class="nav-item dropdown {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-fire"></i><span>Dashboard</span></a>
                <ul class="dropdown-menu">
                    {{-- Bagian ini sudah benar --}}
                    <li class='{{ request()->routeIs('dashboard.index') ? 'active' : '' }}'>
                        <a class="nav-link" href="{{ route('dashboard.index') }}">General Dashboard</a>
                    </li>
                </ul>
            </li>

            
            {{-- MENU BARU UNTUK MANAJEMEN ASET --}}
            <li class="menu-header">Manajemen Aset</li>
            <li class="nav-item dropdown {{ Request::is('kategori*', 'master-data*', 'vendor*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-database"></i> <span>Data Master</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('kategori*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('kategori.index') }}">Kategori</a>
                    </li>
                    <li class="{{ Request::is('master-data*') ? 'active' : '' }}">
                        {{-- 2. Perbaiki link ini agar menjadi teks biasa --}}
                        <a class="nav-link" href="{{ route('master-data.index') }}">Lokasi & Departemen</a>
                    </li>
                    {{-- 3. Hapus </li> yang error dari sini --}}
                    <li class="{{ Request::is('vendor*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('vendor.index') }}">Vendor</a>
                    </li>
                </ul>
            </li>

            {{-- Tambahkan link Pegawai di sini --}}
            <li class="{{ Request::is('pegawai*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('pegawai.index') }}"><i class="fas fa-users"></i> <span>Pegawai</span></a>
            </li>
            <li class="{{ Request::is('aset*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('aset.index') }}"><i class="fas fa-box"></i> <span>Aset</span></a>
            </li>
            {{-- AKHIR DARI MENU BARU --}}

            <li class="menu-header">Transaksi</li>
            <li class="{{ Request::is('riwayat-service*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('riwayat-service.index') }}"><i class="fas fa-tools"></i> <span>Service Aset</span></a>
            </li>
            <li class="{{ Request::is('pemakaian*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('pemakaian.index') }}"><i class="fas fa-people-carry"></i> <span>Pemakaian Aset</span></a>
            </li>
        </ul>

        <div class="hide-sidebar-mini mt-4 mb-4 p-3">
            <a href="{{ route('profile.edit') }}"
                class="btn btn-primary btn-lg btn-block btn-icon-split">
                <i class="fas fa-rocket"></i> Profile
            </a>
        </div>
    </aside>
</div>
