@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('reports.sales') }}">Laporan</a></li>
<li class="breadcrumb-item active">Shift #{{ $shift->id }}</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="card-title mb-4">Laporan Shift #{{ $shift->id }}</h5>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white border-0">
                    <div class="card-body py-3">
                        <p class="small mb-1">Total Penjualan</p>
                        <h5 class="mb-0">Rp {{ number_format($summary['total_sales'], 0, ',', '.') }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white border-0">
                    <div class="card-body py-3">
                        <p class="small mb-1">Transaksi</p>
                        <h5 class="mb-0">{{ $summary['total_transactions'] }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white border-0">
                    <div class="card-body py-3">
                        <p class="small mb-1">Rata-rata</p>
                        <h5 class="mb-0">Rp {{ number_format($summary['average_ticket'], 0, ',', '.') }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card {{ $summary['difference'] >= 0 ? 'bg-success' : 'bg-danger' }} text-white border-0">
                    <div class="card-body py-3">
                        <p class="small mb-1">Selisih Kas</p>
                        <h5 class="mb-0">Rp {{ number_format(abs($summary['difference']), 0, ',', '.') }} {{ $summary['difference'] >= 0 ? '(+)' : '(-)' }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <h6 class="fw-semibold mb-3">Pembayaran per Metode</h6>
        <div class="row g-3 mb-4">
            @foreach($payment_breakdown as $method => $amount)
            <div class="col-md-3">
                <div class="card border">
                    <div class="card-body py-2">
                        <p class="small text-muted mb-1">{{ $method }}</p>
                        <p class="fw-semibold mb-0">Rp {{ number_format($amount, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <h6 class="fw-semibold mb-3">Daftar Transaksi</h6>
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Invoice</th>
                        <th>Waktu</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                    <tr>
                        <td>{{ $sale->invoice_number }}</td>
                        <td>{{ $sale->sale_date->format('H:i') }}</td>
                        <td class="text-end">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="badge {{ $sale->status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">{{ $sale->status }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Tidak ada transaksi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
