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
    <div class="col-lg-8">
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

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Shift Aktif</h5>
                @if($active_shift ?? false)
                    <div class="d-flex align-items-center mb-4 p-3 bg-light rounded-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;">
                            <i class="bi bi-person-check fs-4"></i>
                        </div>
                        <div>
                            <div class="small text-muted">Kasir Bertugas</div>
                            <div class="fw-bold">{{ $active_shift->user->name }}</div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Mulai Shift</span>
                            <span class="fw-medium small">{{ $active_shift->opened_at->format('H:i') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Modal Awal</span>
                            <span class="fw-medium small">Rp {{ number_format($active_shift->opening_cash, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <form action="{{ route('shifts.close', $active_shift) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="small text-muted">Uang Tunai Akhir</label>
                            <input type="number" name="closing_cash" class="form-control" required value="0">
                        </div>
                        <button type="submit" class="btn btn-danger w-100 py-2 fw-bold rounded-3">
                            <i class="bi bi-lock me-2"></i> Tutup Shift
                        </button>
                    </form>
                @else
                    <div class="text-center py-4">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:64px; height:64px;">
                            <i class="bi bi-clock-history fs-2 text-muted"></i>
                        </div>
                        <p class="text-muted mb-4">Tidak ada shift yang aktif saat ini.</p>
                        <form action="{{ route('shifts.open') }}" method="POST">
                            @csrf
                            <input type="hidden" name="opening_cash" value="0">
                            <button type="submit" class="btn btn-success w-100 py-2 fw-bold rounded-3">
                                <i class="bi bi-unlock me-2"></i> Buka Shift Baru
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
