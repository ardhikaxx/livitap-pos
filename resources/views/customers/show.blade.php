@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Pelanggan</a></li>
<li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="card-title mb-4">Detail Pelanggan</h5>

        <div class="row g-4">
            <div class="col-md-6">
                <h6 class="fw-semibold mb-3">Informasi Dasar</h6>
                <table class="table table-sm">
                    <tr><th style="width:140px">Nama</th><td>{{ $customer->name }}</td></tr>
                    <tr><th>Telepon</th><td>{{ $customer->phone }}</td></tr>
                    <tr><th>Email</th><td>{{ $customer->email ?? '-' }}</td></tr>
                    <tr><th>Alamat</th><td>{{ $customer->address ?? '-' }}</td></tr>
                    <tr><th>Tier</th><td>{{ ucfirst($customer->tier) }}</td></tr>
                    <tr><th>Poin</th><td>{{ $customer->points }}</td></tr>
                    <tr><th>Limit Kredit</th><td>Rp {{ number_format($customer->credit_limit ?? 0, 0, ',', '.') }}</td></tr>
                </table>
            </div>

            <div class="col-md-6">
                <h6 class="fw-semibold mb-3">Riwayat Transaksi Terbaru</h6>
                @forelse($customer->sales as $sale)
                    <div class="border rounded p-2 mb-2">
                        <div class="d-flex justify-content-between">
                            <span>{{ $sale->invoice_number }}</span>
                            <strong>Rp {{ number_format($sale->total, 0, ',', '.') }}</strong>
                        </div>
                        <small class="text-muted">{{ $sale->sale_date->format('d/m/Y H:i') }}</small>
                    </div>
                @empty
                    <p class="text-muted">Belum ada transaksi</p>
                @endforelse
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary">Edit Pelanggan</a>
            <a href="{{ route('customers.points', $customer) }}" class="btn btn-success">Riwayat Poin</a>
            <a href="{{ route('customers.transactions', $customer) }}" class="btn btn-info text-white">Semua Transaksi</a>
        </div>
    </div>
</div>
@endsection
