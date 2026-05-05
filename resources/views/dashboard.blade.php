@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="fw-bold mb-0">Dashboard</h4>
    <div class="text-muted small">{{ now()->format('d F Y') }}</div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2">
                        <i class="bi bi-currency-dollar fs-4"></i>
                    </div>
                </div>
                <p class="text-muted small mb-1 fw-medium">Penjualan Hari Ini</p>
                <h4 class="fw-bold mb-0">Rp {{ number_format($today_sales ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-2">
                        <i class="bi bi-receipt fs-4"></i>
                    </div>
                </div>
                <p class="text-muted small mb-1 fw-medium">Transaksi</p>
                <h4 class="fw-bold mb-0">{{ $today_transactions ?? 0 }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="bg-info bg-opacity-10 text-info rounded-3 p-2">
                        <i class="bi bi-wallet2 fs-4"></i>
                    </div>
                </div>
                <p class="text-muted small mb-1 fw-medium">Omzet Bersih</p>
                <h4 class="fw-bold mb-0">Rp {{ number_format($today_net_sales ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2">
                        <i class="bi bi-graph-up fs-4"></i>
                    </div>
                    <span class="badge {{ ($growth ?? 0) >= 0 ? 'bg-success' : 'bg-danger' }} bg-opacity-10 {{ ($growth ?? 0) >= 0 ? 'text-success' : 'text-danger' }} rounded-pill px-2">
                        <i class="bi {{ ($growth ?? 0) >= 0 ? 'bi-arrow-up' : 'bi-arrow-down' }} small"></i> {{ abs(number_format($growth ?? 0, 1)) }}%
                    </span>
                </div>
                <p class="text-muted small mb-1 fw-medium">Growth vs Kemarin</p>
                <h4 class="fw-bold mb-0">
                    {{ number_format($growth ?? 0, 1) }}%
                </h4>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h5 class="fw-bold mb-0">Produk Terlaris</h5>
                    <a href="{{ route('reports.sales') }}" class="btn btn-sm btn-light text-primary fw-bold">Lihat Semua</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light bg-opacity-50">
                            <tr>
                                <th class="border-0 text-muted small fw-bold text-uppercase px-3 py-3">Produk</th>
                                <th class="border-0 text-muted small fw-bold text-uppercase px-3 py-3 text-end">Terjual</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($top_product_today ?? collect()) as $product)
                            <tr>
                                <td class="px-3 py-3">
                                    <div class="fw-semibold">{{ $product->product->name ?? 'Unknown' }}</div>
                                    <div class="text-muted small">{{ $product->product->category->name ?? 'Umum' }}</div>
                                </td>
                                <td class="px-3 py-3 text-end fw-bold">{{ (int) $product->total_qty }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 opacity-25"></i>
                                    Belum ada transaksi hari ini
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
