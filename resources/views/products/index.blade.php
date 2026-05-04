@extends('layouts.app')
@section('title', 'Produk')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Manajemen Produk</h1>
    
    <div class="bg-white rounded shadow p-4">
        <table class="min-w-full">
            <thead>
                <tr class="border-b">
                    <th class="text-left p-2">Nama</th>
                    <th class="text-left p-2">SKU</th>
                    <th class="text-left p-2">Kategori</th>
                    <th class="text-left p-2">Harga Jual</th>
                    <th class="text-left p-2">Stok</th>
                    <th class="text-left p-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    @php $price = $product->prices->first()?->sell_price ?? 0; @endphp
                    <tr class="border-b">
                        <td class="p-2">{{ $product->name }}</td>
                        <td class="p-2">{{ $product->sku }}</td>
                        <td class="p-2">{{ $product->category->name ?? '-' }}</td>
                        <td class="p-2">Rp {{ number_format($price, 0, ',', '.') }}</td>
                        <td class="p-2">{{ $product->stocks->first()?->qty ?? 0 }}</td>
                        <td class="p-2">
                            <a href="{{ route('products.edit', $product) }}" class="text-blue-600">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-4 text-center">Tidak ada produk</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection