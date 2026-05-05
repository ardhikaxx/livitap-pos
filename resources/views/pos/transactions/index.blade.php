@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('pos.index') }}">POS</a></li>
<li class="breadcrumb-item active">Riwayat Transaksi</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="fw-bold mb-0">Riwayat Transaksi</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4">Invoice</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $transaction)
                    <tr>
                        <td class="px-4 fw-bold text-primary">{{ $transaction->invoice_number }}</td>
                        <td>{{ $transaction->sale_date instanceof \DateTime ? $transaction->sale_date->format('d M Y H:i') : \Carbon\Carbon::parse($transaction->sale_date)->format('d M Y H:i') }}</td>
                        <td>{{ $transaction->customer->name ?? 'Umum' }}</td>
                        <td>Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge {{ $transaction->status == 'paid' ? 'bg-success' : 'bg-danger' }}">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </td>
                        <td class="px-4">
                            <a href="{{ route('pos.receipt', $transaction->id) }}" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-printer"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
