@extends('layouts.app')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-6">Laporan Penjualan</h1>

    <form method="GET" class="mb-6 flex flex-wrap gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Dari Tanggal</label>
            <input type="date" name="date_from" value="{{ $from->format('Y-m-d') }}" class="border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Sampai Tanggal</label>
            <input type="date" name="date_to" value="{{ $to->format('Y-m-d') }}" class="border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Kasir</label>
            <select name="user_id" class="border rounded px-3 py-2">
                <option value="">Semua Kasir</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Filter
            </button>
        </div>
    </form>

    <div class="grid grid-cols-4 gap-6 mb-8">
        <div class="bg-blue-50 p-4 rounded">
            <p class="text-sm text-gray-600">Total Penjualan</p>
            <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($summary['total_sales'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-green-50 p-4 rounded">
            <p class="text-sm text-gray-600">Total Transaksi</p>
            <p class="text-2xl font-bold text-green-600">{{ $summary['total_transactions'] }}</p>
        </div>
        <div class="bg-purple-50 p-4 rounded">
            <p class="text-sm text-gray-600">Rata-rata per Transaksi</p>
            <p class="text-2xl font-bold text-purple-600">Rp {{ number_format($summary['average_per_transaction'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-orange-50 p-4 rounded">
            <p class="text-sm text-gray-600">Total Item Terjual</p>
            <p class="text-2xl font-bold text-orange-600">{{ $summary['total_items'] }}</p>
        </div>
    </div>

    <div class="mb-8">
        <h2 class="text-lg font-semibold mb-4">Pembayaran per Metode</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($paymentMethods as $method => $amount)
                <div class="border p-4 rounded">
                    <p class="text-sm text-gray-600 capitalize">{{ str_replace('_', ' ', $method) }}</p>
                    <p class="text-xl font-semibold">Rp {{ number_format($amount, 0, ',', '.') }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <div>
        <h2 class="text-lg font-semibold mb-4">Daftar Transaksi</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">No. Invoice</th>
                        <th class="px-4 py-2 text-left">Tanggal</th>
                        <th class="px-4 py-2 text-left">Kasir</th>
                        <th class="px-4 py-2 text-right">Total</th>
                        <th class="px-4 py-2 text-center">Status</th>
                        <th class="px-4 py-2 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $sale->invoice_number }}</td>
                        <td class="px-4 py-3">{{ $sale->sale_date->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $sale->user->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded text-xs {{ $sale->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $sale->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('pos.receipt', $sale) }}" target="_blank" class="text-blue-500 hover:underline">Struk</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            Belum ada transaksi pada periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $sales->links() }}
        </div>
    </div>
</div>
@endsection
