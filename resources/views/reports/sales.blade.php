@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item active">Laporan Penjualan</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="card-title mb-4">Laporan Penjualan</h5>

        <form method="GET" class="row g-2 mb-4">
            <div class="col-md-3">
                <label class="form-label form-label-sm">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ $from->format('Y-m-d') }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label form-label-sm">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ $to->format('Y-m-d') }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label form-label-sm">Kasir</label>
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">Semua Kasir</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-auto d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            </div>
        </form>

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
                        <p class="small mb-1">Total Transaksi</p>
                        <h5 class="mb-0">{{ $summary['total_transactions'] }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white border-0">
                    <div class="card-body py-3">
                        <p class="small mb-1">Rata-rata per Transaksi</p>
                        <h5 class="mb-0">Rp {{ number_format($summary['average_per_transaction'], 0, ',', '.') }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark border-0">
                    <div class="card-body py-3">
                        <p class="small mb-1">Total Item Terjual</p>
                        <h5 class="mb-0">{{ $summary['total_items'] }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <h6 class="fw-semibold mb-3">Pembayaran per Metode</h6>
        <div class="row g-3 mb-4">
            @foreach($paymentMethods as $method => $amount)
            <div class="col-md-3">
                <div class="card border">
                    <div class="card-body py-2">
                        <p class="small text-muted mb-1">{{ str_replace('_', ' ', $method) }}</p>
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
                        <th>No. Invoice</th>
                        <th>Tanggal</th>
                        <th>Kasir</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                    <tr>
                        <td>{{ $sale->invoice_number }}</td>
                        <td>{{ $sale->sale_date->format('d/m/Y H:i') }}</td>
                        <td>{{ $sale->user->name ?? '-' }}</td>
                        <td class="text-end">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="badge {{ $sale->status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $sale->status }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('pos.receipt', $sale) }}" target="_blank" class="btn btn-outline-secondary btn-sm">Struk</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Belum ada transaksi pada periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $sales->links() }}</div>
    </div>
</div>
@endsection
