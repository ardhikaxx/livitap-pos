@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produk</a></li>
<li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="card-title mb-4">Tambah Produk Baru</h5>

        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Nama Produk *</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Kategori</label>
                    <select name="category_id" class="form-select">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">SKU *</label>
                    <input type="text" name="sku" value="{{ old('sku') }}" class="form-control @error('sku') is-invalid @enderror" required>
                    @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Barcode (opsional)</label>
                    <input type="text" name="barcode" value="{{ old('barcode') }}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Satuan</label>
                    <select name="unit" class="form-select">
                        <option value="pcs" {{ old('unit') == 'pcs' ? 'selected' : '' }}>Pcs</option>
                        <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Kg</option>
                        <option value="gram" {{ old('unit') == 'gram' ? 'selected' : '' }}>Gram</option>
                        <option value="liter" {{ old('unit') == 'liter' ? 'selected' : '' }}>Liter</option>
                        <option value="box" {{ old('unit') == 'box' ? 'selected' : '' }}>Box</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Harga Beli (HPP) *</label>
                    <input type="number" name="buy_price" value="{{ old('buy_price', 0) }}" step="100" class="form-control @error('buy_price') is-invalid @enderror" required>
                    @error('buy_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Harga Jual *</label>
                    <input type="number" name="sell_price" value="{{ old('sell_price', 0) }}" step="100" class="form-control @error('sell_price') is-invalid @enderror" required>
                    @error('sell_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Stok Minimum</label>
                    <input type="number" name="min_stock" value="{{ old('min_stock', 0) }}" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Deskripsi</label>
                    <textarea name="description" rows="3" class="form-control">{{ old('description') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium d-block">Opsi</label>
                    <div class="form-check">
                        <input type="checkbox" name="track_stock" id="track_stock" {{ old('track_stock', true) ? 'checked' : '' }} class="form-check-input">
                        <label class="form-check-label" for="track_stock">Lacak Stok</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_active" id="is_active" {{ old('is_active', true) ? 'checked' : '' }} class="form-check-input">
                        <label class="form-check-label" for="is_active">Aktif</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_pos_visible" id="is_pos_visible" {{ old('is_pos_visible', true) ? 'checked' : '' }} class="form-check-input">
                        <label class="form-check-label" for="is_pos_visible">Tampilkan di POS</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_favorite" id="is_favorite" {{ old('is_favorite') ? 'checked' : '' }} class="form-check-input">
                        <label class="form-check-label" for="is_favorite">Produk Favorit</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Foto Produk</label>
                    <input type="file" name="photo" accept="image/*" class="form-control">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Produk</button>
            </div>
        </form>
    </div>
</div>
@endsection
