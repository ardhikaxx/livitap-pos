@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item active">Daftar Pelanggan</li>
@endsection

@section('content')
<h4 class="fw-bold mb-4">Daftar Pelanggan</h4>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3">Nama Pelanggan</th>
                        <th class="py-3">Total Transaksi</th>
                        <th class="py-3">Total Belanja</th>
                        <th class="px-4 py-3">Terakhir Belanja</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $customer)
                    <tr>
                        <td class="px-4 fw-bold">{{ $customer->name }}</td>
                        <td>{{ $customer->sales_count }} kali</td>
                        <td>Rp {{ number_format($customer->sales_sum_total ?? 0, 0, ',', '.') }}</td>
                        <td class="px-4 text-muted small">
                            {{ $customer->sales->first()?->sale_date?->format('d M Y') ?? '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0 py-3">
        {{ $customers->links() }}
    </div>
</div>
@endsection
