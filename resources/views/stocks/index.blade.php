@extends('layouts.app')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-6">Stok Produk</h1>

    <form method="GET" class="mb-6">
        <input type="text" name="search" value="{{ request('search') }}" 
            placeholder="Cari produk..." 
            class="border rounded px-4 py-2 w-64">
        <button type="submit" class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300 ml-2">Cari</button>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">Produk</th>
                    <th class="px-4 py-2 text-right">Stok</th>
                    <th class="px-4 py-2 text-right">Min</th>
                    <th class="px-4 py-2 text-right">Max</th>
                    <th class="px-4 py-2 text-center">Status</th>
                    <th class="px-4 py-2 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stocks as $stock)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $stock->product->name }}</td>
                    <td class="px-4 py-3 text-right">{{ $stock->qty }}</td>
                    <td class="px-4 py-3 text-right">{{ $stock->min_qty }}</td>
                    <td class="px-4 py-3 text-right">{{ $stock->max_qty ?? '-' }}</td>
                    <td class="px-4 py-3 text-center">
                        @if($stock->qty <= $stock->min_qty && $stock->min_qty > 0)
                            <span class="text-red-600 font-bold">LOW STOCK</span>
                        @else
                            <span class="text-green-600">OK</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('stocks.adjust', $stock->product) }}" class="text-blue-500 hover:underline">Adjust</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada data stok.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $stocks->withQueryString()->links() }}
    </div>
</div>
@endsection
