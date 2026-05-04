@extends('layouts.app')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">{{ $product->name }}</h1>
        <a href="{{ route('products.index') }}" class="text-blue-600 hover:underline">&laquo; Kembali</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2">
            <dl class="space-y-4">
                <div>
                    <dt class="font-medium text-gray-600">Kategori</dt>
                    <dd>{{ $product->category->name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-600">SKU</dt>
                    <dd>{{ $product->sku }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-600">Barcode</dt>
                    <dd>{{ $product->barcode ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-600">Satuan</dt>
                    <dd>{{ $product->unit }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-600">Harga Beli</dt>
                    <dd>Rp {{ number_format($product->prices->first()?->buy_price ?? 0, 0, ',', '.') }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-600">Harga Jual</dt>
                    <dd>Rp {{ number_format($product->prices->first()?->sell_price ?? 0, 0, ',', '.') }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-gray-600">Stok</dt>
                    <dd>{{ $product->stocks->sum('qty') ?? 0 }}</dd>
                </div>
            </dl>
        </div>
        <div>
            @if($product->photo)
                <img src="{{ asset('storage/' . $product->photo) }}" alt="{{ $product->name }}" class="w-full rounded">
            @endif
            <div class="mt-4 space-y-2">
                <a href="{{ route('products.edit', $product) }}" class="block w-full text-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Edit</a>
                <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Hapus produk ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection