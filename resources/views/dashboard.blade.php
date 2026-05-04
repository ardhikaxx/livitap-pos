@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm font-medium">Penjualan Hari Ini</h3>
        <p class="text-2xl font-bold text-gray-900 mt-2">
            Rp {{ number_format($today_sales ?? 0, 0, ',', '.') }}
        </p>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm font-medium">Transaksi</h3>
        <p class="text-2xl font-bold text-gray-900 mt-2">
            {{ $today_transactions ?? 0 }}
        </p>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm font-medium">Tiket Rata-rata</h3>
        <p class="text-2xl font-bold text-gray-900 mt-2">
            Rp {{ number_format($average_ticket ?? 0, 0, ',', '.') }}
        </p>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm font-medium">Growth vs Kemarin</h3>
        <p class="text-2xl font-bold mt-2 
            {{ ($growth ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
            {{ number_format($growth ?? 0, 1) }}%
        </p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Produk Terlaris Hari Ini</h3>
        <table class="min-w-full">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-2">Produk</th>
                    <th class="text-right py-2">Qty</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($top_product_today ?? collect()) as $product)
                <tr class="border-b">
                    <td class="py-2">{{ $product->product->name ?? 'Unknown' }}</td>
                    <td class="text-right py-2">{{ $product->total_qty ?? 0 }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="text-center py-4 text-gray-500">Belum ada transaksi</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Shift Aktif</h3>
        @if($active_shift)
            <div class="space-y-2">
                <p><strong>Kasir:</strong> {{ $active_shift->user->name }}</p>
                <p><strong>Mulai:</strong> {{ $active_shift->opened_at->format('d/m/Y H:i') }}</p>
                <p><strong>Modal Awal:</strong> Rp {{ number_format($active_shift->opening_cash, 0, ',', '.') }}</p>
                <form action="{{ route('shifts.close', $active_shift) }}" method="POST">
                    @csrf
                    @method('POST')
                    <button type="submit" class="mt-4 bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                        Tutup Shift
                    </button>
                </form>
            </div>
        @else
            <p class="text-gray-500">Tidak ada shift aktif</p>
            <a href="{{ route('shifts.open') }}" class="mt-4 inline-block bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                Buka Shift
            </a>
        @endif
    </div>
</div>
@endsection
