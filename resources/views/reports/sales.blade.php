@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item active">Laporan Penjualan</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Laporan Penjualan</h4>
        <p class="text-muted small mb-0">Analisis kinerja penjualan Anda dalam satu tampilan.</p>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4 rounded-3">
    <div class="card-body p-4">
        <form method="GET" class="row g-4 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-uppercase text-muted">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ $from->format('Y-m-d') }}" class="form-control form-control-lg shadow-none border-light">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-uppercase text-muted">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ $to->format('Y-m-d') }}" class="form-control form-control-lg shadow-none border-light">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-uppercase text-muted">Kasir</label>
                <select name="user_id" class="form-select form-select-lg shadow-none border-light">
                    <option value="">Semua Kasir</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm">
                    <i class="bi bi-funnel me-2"></i> Terapkan Filter
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    @php
    $stats = [
        ['label' => 'Total Penjualan', 'value' => 'Rp ' . number_format($summary['total_sales'], 0, ',', '.'), 'icon' => 'bi-graph-up-arrow', 'color' => 'primary'],
        ['label' => 'Total Transaksi', 'value' => $summary['total_transactions'], 'icon' => 'bi-receipt-cutoff', 'color' => 'success'],
        ['label' => 'Rata-rata/Transaksi', 'value' => 'Rp ' . number_format($summary['average_per_transaction'], 0, ',', '.'), 'icon' => 'bi-wallet2', 'color' => 'info'],
        ['label' => 'Total Item Terjual', 'value' => $summary['total_items'], 'icon' => 'bi-box-seam', 'color' => 'warning'],
    ];
    @endphp

    @foreach($stats as $stat)
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="rounded-3 bg-{{ $stat['color'] }} bg-opacity-10 text-{{ $stat['color'] }} p-3 me-3">
                    <i class="bi {{ $stat['icon'] }} fs-4"></i>
                </div>
                <div>
                    <p class="text-muted small mb-0">{{ $stat['label'] }}</p>
                    <h5 class="fw-bold mb-0 text-dark">{{ $stat['value'] }}</h5>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white border-0 p-4 pb-0 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0">Daftar Transaksi Lengkap</h6>
        <form action="{{ route('reports.export') }}" method="POST">
            @csrf
            <input type="hidden" name="type" value="excel">
            <input type="hidden" name="report" value="sales">
            <input type="hidden" name="date_from" value="{{ request('date_from', $from->format('Y-m-d')) }}">
            <input type="hidden" name="date_to" value="{{ request('date_to', $to->format('Y-m-d')) }}">
            <input type="hidden" name="user_id" value="{{ request('user_id') }}">
            <button type="submit" class="btn btn-sm btn-light text-muted fw-bold shadow-sm">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export Excel
            </button>
        </form>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="text-uppercase small text-muted">
                    <tr>
                        <th class="px-3 py-3 border-0">No. Invoice</th>
                        <th class="px-3 py-3 border-0">Tanggal</th>
                        <th class="px-3 py-3 border-0">Kasir</th>
                        <th class="px-3 py-3 border-0 text-end">Total</th>
                        <th class="px-3 py-3 border-0 text-center">Status</th>
                        <th class="px-3 py-3 border-0 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                    <tr>
                        <td class="px-3 py-3 fw-bold text-dark">{{ $sale->invoice_number }}</td>
                        <td class="px-3 py-3 text-muted">{{ $sale->sale_date->format('d/m/Y H:i') }}</td>
                        <td class="px-3 py-3 fw-medium">{{ $sale->user->name ?? '-' }}</td>
                        <td class="px-3 py-3 text-end fw-bold text-primary">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-center">
                            <span class="badge {{ $sale->status === 'paid' ? 'bg-success' : 'bg-warning' }} rounded-pill px-3 py-2">
                                {{ ucfirst($sale->status) }}
                            </span>
                        </td>
                        <td class="px-3 py-3 text-end">
                            <a href="{{ route('pos.receipt', $sale) }}" target="_blank" class="btn btn-sm btn-light border-0 shadow-none fw-bold text-primary">
                                <i class="bi bi-printer me-1"></i> Struk
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">Belum ada transaksi pada periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $sales->links() }}</div>
    </div>
</div>
@endsection
