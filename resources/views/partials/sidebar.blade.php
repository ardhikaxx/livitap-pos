<nav id="sidebar" class="d-flex flex-column flex-shrink-0 bg-dark text-white" style="width: 250px; min-height: 100vh;">
    <a href="{{ route('pos.index') }}" class="d-flex align-items-center p-3 text-white text-decoration-none border-bottom border-secondary">
        <span class="fs-5 fw-bold">🏪 LIVITAP POS</span>
    </a>

    <ul class="nav nav-pills flex-column mb-auto p-2">
        <li class="nav-item">
            <a href="{{ route('pos.index') }}" class="nav-link text-white {{ request()->routeIs('pos.*') ? 'active' : '' }}">
                🛒 Kasir (POS)
            </a>
        </li>
        <li class="mt-2 mb-1 px-2">
            <small class="text-secondary text-uppercase fw-bold" style="font-size: 0.7rem;">Manajemen</small>
        </li>
        <li class="nav-item">
            <a href="{{ route('products.index') }}" class="nav-link text-white {{ request()->routeIs('products.*') ? 'active' : '' }}">
                📦 Produk
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('stocks.index') }}" class="nav-link text-white {{ request()->routeIs('stocks.*') ? 'active' : '' }}">
                📋 Stok
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('customers.index') }}" class="nav-link text-white {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                👥 Pelanggan
            </a>
        </li>

        @php
            $isFnb = $activeBusiness && (
                $activeBusiness->type === 'fnb' ||
                !empty(($activeBusiness->settings['enable_fnb'] ?? false))
            );
        @endphp

        @if($isFnb)
        <li class="nav-item">
            <a href="{{ route('tables.index') }}" class="nav-link text-white {{ request()->routeIs('tables.*') ? 'active' : '' }}">
                🪑 Meja
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('kitchen.orders') }}" class="nav-link text-white {{ request()->routeIs('kitchen.*') ? 'active' : '' }}">
                🍳 Dapur
            </a>
        </li>
        @endif

        <li class="mt-2 mb-1 px-2">
            <small class="text-secondary text-uppercase fw-bold" style="font-size: 0.7rem;">Laporan</small>
        </li>
        <li class="nav-item">
            <a href="{{ route('reports.sales') }}" class="nav-link text-white {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                📈 Laporan Penjualan
            </a>
        </li>

        <li class="mt-2 mb-1 px-2">
            <small class="text-secondary text-uppercase fw-bold" style="font-size: 0.7rem;">Pengaturan</small>
        </li>
        <li class="nav-item">
            <a href="{{ route('settings.business') }}" class="nav-link text-white {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                ⚙️ Pengaturan
            </a>
        </li>
    </ul>

    <div class="border-top border-secondary p-3">
        <div class="d-flex align-items-center mb-2">
            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-2" style="width:32px;height:32px;">
                <span class="text-white fw-bold" style="font-size:0.8rem;">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
            </div>
            <small class="text-white">{{ auth()->user()->name ?? 'User' }}</small>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-danger w-100">Logout</button>
        </form>
    </div>
</nav>
