@extends('layouts.app')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-6">Edit Produk</h1>

    <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block font-medium mb-2">Nama Produk</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" class="w-full border rounded px-3 py-2 @error('name') border-red-500 @enderror" required>
                @error('name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block font-medium mb-2">Kategori</label>
                <select name="category_id" class="w-full border rounded px-3 py-2">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-medium mb-2">SKU</label>
                <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="w-full border rounded px-3 py-2 @error('sku') border-red-500 @enderror" required>
                @error('sku')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block font-medium mb-2">Barcode</label>
                <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}" class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block font-medium mb-2">Harga Beli (HPP) *</label>
                <input type="number" name="buy_price" value="{{ old('buy_price', $product->prices->first()?->buy_price ?? 0) }}" step="100" class="w-full border rounded px-3 py-2 @error('buy_price') border-red-500 @enderror" required>
                @error('buy_price')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block font-medium mb-2">Harga Jual *</label>
                <input type="number" name="sell_price" value="{{ old('sell_price', $product->prices->first()?->sell_price ?? 0) }}" step="100" class="w-full border rounded px-3 py-2 @error('sell_price') border-red-500 @enderror" required>
                @error('sell_price')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div class="md:col-span-2">
                <label class="block font-medium mb-2">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full border rounded px-3 py-2">{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="checkbox" name="track_stock" {{ old('track_stock', $product->track_stock) ? 'checked' : '' }} class="rounded">
                    <span class="ml-2">Lacak Stok</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="rounded">
                    <span class="ml-2">Aktif</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="is_pos_visible" {{ old('is_pos_visible', $product->is_pos_visible) ? 'checked' : '' }} class="rounded">
                    <span class="ml-2">Tampilkan di POS</span>
                </label>
            </div>

            @if($product->photo)
            <div>
                <label class="block font-medium mb-2">Foto Saat Ini</label>
                <img src="{{ asset('storage/' . $product->photo) }}" alt="{{ $product->name }}" class="w-32 h-32 object-cover rounded">
            </div>
            @endif
        </div>

        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('products.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                Batal
            </a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Update Produk
            </button>
        </div>
    </form>
</div>
@endsection
