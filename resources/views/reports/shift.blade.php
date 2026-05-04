@extends('layouts.app')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-6">Laporan Shift #{{ $shift->id }}</h1>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-blue-50 p-4 rounded">
            <p class="text-sm text-gray-600">Total Penjualan</p>
            <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($summary['total_sales'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-green-50 p-4 rounded">
            <p class="text-sm text-gray-600">Transaksi</p>
            <p class="text-2xl font-bold text-green-600">{{ $summary['total_transactions'] }}</p>
        </div>
        <div class="bg-purple-50 p-4 rounded">
            <p class="text-sm text-gray-600">Rata-rata</p>
            <p class="text-2xl font-bold text-purple-600">Rp {{ number_format($summary['average_ticket'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-orange-50 p-4 rounded">
            <p class="text-sm text-gray-600">Selisih Kas</p>
            <p class="text-2xl font-bold {{ $summary['difference'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                Rp {{ number_format(abs($summary['difference']), 0, ',', '.') }}
                {{ $summary['difference'] >= 0 ? '(+)' : '(-)' }}
            </p>
        </div>
    </div>

    <div class="mb-8">
        <h2 class="text-lg font-semibold mb-4">Pembayaran per Metode</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($payment_breakdown as $method => $amount)
                <div class="border p-4 rounded">
                    <p class="text-sm text-gray-600">{{ $method }}</p>
                    <p class="text-xl font-semibold">Rp {{ number_format($amount, 0, ',', '.') }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <div>
        <h2 class="text-lg font-semibold mb-4">Daftar Transaksi</h2>
        <table class="min-w-full border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">Invoice</th>
                    <th class="px-4 py-2 text-left">Waktu</th>
                    <th class="px-4 py-2 text-right">Total</th>
                    <th class="px-4 py-2 text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                <tr class="border-b">
                    <td class="px-4 py-3">{{ $sale->invoice_number }}</td>
                    <td class="px-4 py-3">{{ $sale->sale_date->format('H:i') }}</td>
                    <td class="px-4 py-3 text-right">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-center">{{ $sale->status }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">Tidak ada transaksi</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
