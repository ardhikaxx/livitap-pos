@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produk</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="card-title mb-4">Edit Produk</h5>

        <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Nama Produk *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Kategori</label>
                    <select name="category_id" class="form-select">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">SKU *</label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="form-control @error('sku') is-invalid @enderror" required>
                    @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Barcode</label>
                    <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Harga Beli (HPP) *</label>
                    <input type="number" name="buy_price" value="{{ old('buy_price', $product->prices->first()?->buy_price ?? 0) }}" step="100" class="form-control @error('buy_price') is-invalid @enderror" required>
                    @error('buy_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Harga Jual *</label>
                    <input type="number" name="sell_price" value="{{ old('sell_price', $product->prices->first()?->sell_price ?? 0) }}" step="100" class="form-control @error('sell_price') is-invalid @enderror" required>
                    @error('sell_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Deskripsi</label>
                    <textarea name="description" rows="3" class="form-control">{{ old('description', $product->description) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium d-block">Opsi</label>
                    <div class="form-check">
                        <input type="checkbox" name="track_stock" id="track_stock" {{ old('track_stock', $product->track_stock) ? 'checked' : '' }} class="form-check-input">
                        <label class="form-check-label" for="track_stock">Lacak Stok</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_active" id="is_active" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="form-check-input">
                        <label class="form-check-label" for="is_active">Aktif</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_pos_visible" id="is_pos_visible" {{ old('is_pos_visible', $product->is_pos_visible) ? 'checked' : '' }} class="form-check-input">
                        <label class="form-check-label" for="is_pos_visible">Tampilkan di POS</label>
                    </div>
                </div>
                @if($product->photo)
                <div class="col-md-6">
                    <label class="form-label fw-medium">Foto Saat Ini</label><br>
                    <img src="{{ asset('storage/' . $product->photo) }}" alt="{{ $product->name }}" class="img-thumbnail" style="width:128px;height:128px;object-fit:cover;">
                </div>
                @endif
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Update Produk</button>
            </div>
        </form>
    </div>
</div>
@endsection
