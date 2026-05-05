@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produk</a></li>
<li class="breadcrumb-item active">{{ $product->name }}</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="card-title mb-0">{{ $product->name }}</h5>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">&laquo; Kembali</a>
        </div>

        <div class="row g-4">
            <div class="col-md-8">
                <table class="table table-sm">
                    <tr><th style="width:160px">Kategori</th><td>{{ $product->category->name ?? '-' }}</td></tr>
                    <tr><th>SKU</th><td>{{ $product->sku }}</td></tr>
                    <tr><th>Barcode</th><td>{{ $product->barcode ?? '-' }}</td></tr>
                    <tr><th>Satuan</th><td>{{ $product->unit }}</td></tr>
                    <tr><th>Harga Beli</th><td>Rp {{ number_format($product->prices->first()?->buy_price ?? 0, 0, ',', '.') }}</td></tr>
                    <tr><th>Harga Jual</th><td>Rp {{ number_format($product->prices->first()?->sell_price ?? 0, 0, ',', '.') }}</td></tr>
                    <tr><th>Stok</th><td>{{ $product->stocks->sum('qty') ?? 0 }}</td></tr>
                </table>
            </div>
            <div class="col-md-4">
                @if($product->photo)
                    <img src="{{ asset('storage/' . $product->photo) }}" alt="{{ $product->name }}" class="img-fluid rounded mb-3">
                @endif
                <div class="d-grid gap-2">
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">Edit</a>
                    <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Hapus produk ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
