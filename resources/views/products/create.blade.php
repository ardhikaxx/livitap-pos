@extends('layouts.app')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-6">Tambah Produk Baru</h1>

    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block font-medium mb-2">Nama Produk</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2 @error('name') border-red-500 @enderror" required>
                @error('name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block font-medium mb-2">Kategori</label>
                <select name="category_id" class="w-full border rounded px-3 py-2">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-medium mb-2">SKU</label>
                <input type="text" name="sku" value="{{ old('sku') }}" class="w-full border rounded px-3 py-2 @error('sku') border-red-500 @enderror" required>
                @error('sku')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block font-medium mb-2">Barcode (opsional)</label>
                <input type="text" name="barcode" value="{{ old('barcode') }}" class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block font-medium mb-2">Satuan</label>
                <select name="unit" class="w-full border rounded px-3 py-2">
                    <option value="pcs" {{ old('unit') == 'pcs' ? 'selected' : '' }}>Pcs</option>
                    <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Kg</option>
                    <option value="gram" {{ old('unit') == 'gram' ? 'selected' : '' }}>Gram</option>
                    <option value="liter" {{ old('unit') == 'liter' ? 'selected' : '' }}>Liter</option>
                    <option value="box" {{ old('unit') == 'box' ? 'selected' : '' }}>Box</option>
                </select>
            </div>

            <div>
                <label class="block font-medium mb-2">Harga Beli (HPP) *</label>
                <input type="number" name="buy_price" value="{{ old('buy_price', 0) }}" step="100" class="w-full border rounded px-3 py-2 @error('buy_price') border-red-500 @enderror" required>
                @error('buy_price')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block font-medium mb-2">Harga Jual *</label>
                <input type="number" name="sell_price" value="{{ old('sell_price', 0) }}" step="100" class="w-full border rounded px-3 py-2 @error('sell_price') border-red-500 @enderror" required>
                @error('sell_price')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>

            <div>
                <label class="block font-medium mb-2">Stok Minimum</label>
                <input type="number" name="min_stock" value="{{ old('min_stock', 0) }}" step="1" class="w-full border rounded px-3 py-2">
            </div>

            <div class="md:col-span-2">
                <label class="block font-medium mb-2">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
            </div>

            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="checkbox" name="track_stock" {{ old('track_stock', true) ? 'checked' : '' }} class="rounded">
                    <span class="ml-2">Lacak Stok</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" {{ old('is_active', true) ? 'checked' : '' }} class="rounded">
                    <span class="ml-2">Aktif</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="is_pos_visible" {{ old('is_pos_visible', true) ? 'checked' : '' }} class="rounded">
                    <span class="ml-2">Tampilkan di POS</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="is_favorite" {{ old('is_favorite') ? 'checked' : '' }} class="rounded">
                    <span class="ml-2">Produk Favorit</span>
                </label>
            </div>

            <div>
                <label class="block font-medium mb-2">Foto Produk</label>
                <input type="file" name="photo" accept="image/*" class="w-full border rounded px-3 py-2">
            </div>
        </div>

        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('products.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                Batal
            </a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Simpan Produk
            </button>
        </div>
    </form>
</div>
@endsection
