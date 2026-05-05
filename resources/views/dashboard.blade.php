@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<h4 class="mb-4">Dashboard</h4>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <p class="text-muted small mb-1">Penjualan Hari Ini</p>
                <h4 class="fw-bold mb-0">Rp {{ number_format($today_sales ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <p class="text-muted small mb-1">Transaksi</p>
                <h4 class="fw-bold mb-0">{{ $today_transactions ?? 0 }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <p class="text-muted small mb-1">Tiket Rata-rata</p>
                <h4 class="fw-bold mb-0">Rp {{ number_format($average_ticket ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <p class="text-muted small mb-1">Growth vs Kemarin</p>
                <h4 class="fw-bold mb-0 {{ ($growth ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ number_format($growth ?? 0, 1) }}%
                </h4>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">Produk Terlaris Hari Ini</h5>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th class="text-end">Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($top_product_today ?? collect()) as $product)
                        <tr>
                            <td>{{ $product->product->name ?? 'Unknown' }}</td>
                            <td class="text-end">{{ $product->total_qty ?? 0 }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted py-3">Belum ada transaksi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">Shift Aktif</h5>
                @if($active_shift ?? false)
                    <p><strong>Kasir:</strong> {{ $active_shift->user->name }}</p>
                    <p><strong>Mulai:</strong> {{ $active_shift->opened_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Modal Awal:</strong> Rp {{ number_format($active_shift->opening_cash, 0, ',', '.') }}</p>
                    <form action="{{ route('shifts.close', $active_shift) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">Tutup Shift</button>
                    </form>
                @else
                    <p class="text-muted">Tidak ada shift aktif</p>
                    <a href="{{ route('shifts.open') }}" class="btn btn-success btn-sm">Buka Shift</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
