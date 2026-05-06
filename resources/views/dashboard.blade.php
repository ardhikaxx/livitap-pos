@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item active text-secondary">Overview</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-0">Dashboard</h3>
        <p class="text-muted small">Selamat datang kembali, {{ auth()->user()->name ?? 'Admin' }}.</p>
    </div>
    <div class="bg-white border rounded-pill px-3 py-2 shadow-sm">
        <i class="bi bi-calendar3 me-2 text-primary"></i>
        <span class="small fw-semibold text-secondary">{{ now()->format('d F Y') }}</span>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm hover-lift" style="transition: transform 0.2s;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-wallet2 fs-4"></i>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted small mb-0 fw-bold text-uppercase">Penjualan</p>
                        <h5 class="fw-bold mb-0">Rp {{ number_format($today_sales ?? 0, 0, ',', '.') }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm hover-lift" style="transition: transform 0.2s;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-receipt fs-4"></i>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted small mb-0 fw-bold text-uppercase">Transaksi</p>
                        <h5 class="fw-bold mb-0">{{ $today_transactions ?? 0 }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm hover-lift" style="transition: transform 0.2s;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-graph-up-arrow fs-4"></i>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted small mb-0 fw-bold text-uppercase">Omzet</p>
                        <h5 class="fw-bold mb-0">Rp {{ number_format($today_net_sales ?? 0, 0, ',', '.') }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm hover-lift" style="transition: transform 0.2s;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-percent fs-4"></i>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted small mb-0 fw-bold text-uppercase">Growth</p>
                        <h5 class="fw-bold mb-0 {{ ($growth ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($growth ?? 0, 1) }}%
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 p-4 pb-0">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-star-fill text-warning me-2"></i>Produk Terlaris Hari Ini
                    </h5>
                    <a href="{{ route('reports.sales') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">Lihat Laporan</a>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="text-muted small text-uppercase">
                            <tr>
                                <th class="border-0 px-3">Produk</th>
                                <th class="border-0 px-3 text-center">Kategori</th>
                                <th class="border-0 px-3 text-end">Terjual</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($top_product_today ?? collect()) as $product)
                            <tr>
                                <td class="px-3 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded p-2 me-3">
                                            <i class="bi bi-box-seam text-secondary"></i>
                                        </div>
                                        <span class="fw-bold text-dark">{{ $product->product->name ?? 'Unknown' }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <span class="badge bg-light text-secondary rounded-pill">{{ $product->product->category->name ?? 'Umum' }}</span>
                                </td>
                                <td class="px-3 py-3 text-end">
                                    <span class="fw-bold text-primary">{{ (int) $product->total_qty }} Unit</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
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

@push('styles')
<style>
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1) !important;
    }
</style>
@endpush
@endsection
