<nav id="sidebar" class="d-flex flex-column flex-shrink-0">
    <div class="p-3 mb-2">
        <a href="{{ route('pos.index') }}" class="d-flex align-items-center text-white text-decoration-none">
            <div class="bg-primary rounded-3 p-1 me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                <i class="bi bi-shop-window text-white"></i>
            </div>
            <span class="fs-5 fw-bold tracking-tight">LIVITAP<span class="text-primary">POS</span></span>
        </a>
    </div>

    <div class="overflow-y-auto flex-grow-1 px-2">
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <li class="mt-4 mb-2 px-3">
                <small class="text-uppercase fw-bold text-secondary" style="font-size: 0.65rem; letter-spacing: 0.05em;">Operasional</small>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('pos.index') }}" class="nav-link {{ request()->routeIs('pos.index') ? 'active' : '' }}">
                    <i class="bi bi-cart3"></i>
                    <span>Kasir (POS)</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('pos.transactions.index') }}" class="nav-link {{ request()->routeIs('pos.transactions.index') ? 'active' : '' }}">
                    <i class="bi bi-list-check"></i>
                    <span>Riwayat Transaksi</span>
                </a>
            </li>

            <li class="mt-4 mb-2 px-3">
                <small class="text-uppercase fw-bold text-secondary" style="font-size: 0.65rem; letter-spacing: 0.05em;">Manajemen</small>
            </li>

            <li class="nav-item">
                <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i>
                    <span>Produk</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i>
                    <span>Kategori</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('stocks.index') }}" class="nav-link {{ request()->routeIs('stocks.*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-check"></i>
                    <span>Stok</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>Pelanggan</span>
                </a>
            </li>

            <li class="mt-4 mb-2 px-3">
                <small class="text-uppercase fw-bold text-secondary" style="font-size: 0.65rem; letter-spacing: 0.05em;">Laporan</small>
            </li>
            <li class="nav-item">
                <a href="{{ route('reports.sales') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span>Laporan Penjualan</span>
                </a>
            </li>

            <li class="mt-4 mb-2 px-3">
                <small class="text-uppercase fw-bold text-secondary" style="font-size: 0.65rem; letter-spacing: 0.05em;">Sistem</small>
            </li>
            <li class="nav-item">
                <a href="{{ route('settings.business') }}" class="nav-link {{ request()->routeIs('settings.business') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i>
                    <span>Pengaturan</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('settings.receipt') }}" class="nav-link {{ request()->routeIs('settings.receipt') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i>
                    <span>Pengaturan Struk</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>Pengguna</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="mt-auto p-3 border-top border-secondary border-opacity-25">
        <div class="bg-secondary bg-opacity-10 rounded-3 p-3">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width:40px;height:40px; flex-shrink: 0;">
                    <span class="text-white fw-bold">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
                </div>
                <div class="overflow-hidden">
                    <div class="text-white fw-medium text-truncate" style="font-size: 0.9rem;">{{ auth()->user()->name ?? 'User' }}</div>
                    <div class="text-secondary text-truncate" style="font-size: 0.75rem;">{{ auth()->user()->email ?? '' }}</div>
                </div>
            </div>
        </div>
    </div>
</nav>
