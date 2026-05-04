@extends('layouts.app')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Manajemen Produk</h1>
        <a href="{{ route('products.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            + Tambah Produk
        </a>
    </div>

    <form method="GET" class="mb-6 flex flex-wrap gap-4">
        <input type="text" name="search" value="{{ request('search') }}" 
            placeholder="Cari nama/SKU..." 
            class="border rounded px-4 py-2 w-64">
            
        <select name="category_id" class="border rounded px-4 py-2">
            <option value="">Semua Kategori</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        <select name="is_active" class="border rounded px-4 py-2">
            <option value="">Semua Status</option>
            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
        </select>

        <button type="submit" class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300">Filter</button>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">Nama</th>
                    <th class="px-4 py-2 text-left">Kategori</th>
                    <th class="px-4 py-2 text-left">SKU</th>
                    <th class="px-4 py-2 text-right">Harga Jual</th>
                    <th class="px-4 py-2 text-right">Stok</th>
                    <th class="px-4 py-2 text-center">Status</th>
                    <th class="px-4 py-2 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <a href="{{ route('products.show', $product) }}" class="hover:text-blue-600">
                            {{ $product->name }}
                        </a>
                    </td>
                    <td class="px-4 py-3">{{ $product->category->name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $product->sku }}</td>
                    <td class="px-4 py-3 text-right">
                        Rp {{ number_format($product->prices->first()?->sell_price ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        {{ $product->stocks->sum('qty') ?? 0 }}
                        @if($product->track_stock && ($product->stocks->sum('qty') <= ($product->stocks->first()?->min_qty ?? 0)))
                            <span class="text-red-500 text-xs">(Low)</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 rounded text-xs {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right space-x-1">
                        <a href="{{ route('products.edit', $product) }}" class="text-blue-500 hover:underline">Edit</a>
                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline" onclick="return confirm('Hapus produk?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                        Belum ada produk.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $products->withQueryString()->links() }}
    </div>
</div>
@endsection
