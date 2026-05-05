@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item active">Produk</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="card-title mb-0">Manajemen Produk</h5>
            <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">+ Tambah Produk</a>
        </div>

        <form method="GET" class="row g-2 mb-4">
            <div class="col-md-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/SKU..." class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="is_active" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>SKU</th>
                        <th class="text-end">Harga Jual</th>
                        <th class="text-end">Stok</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td><a href="{{ route('products.show', $product) }}" class="text-decoration-none">{{ $product->name }}</a></td>
                        <td>{{ $product->category->name ?? '-' }}</td>
                        <td>{{ $product->sku }}</td>
                        <td class="text-end">Rp {{ number_format($product->prices->first()?->sell_price ?? 0, 0, ',', '.') }}</td>
                        <td class="text-end">
                            {{ $product->stocks->sum('qty') ?? 0 }}
                            @if($product->track_stock && ($product->stocks->sum('qty') <= ($product->stocks->first()?->min_qty ?? 0)))
                                <span class="badge bg-danger ms-1">Low</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus produk?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Belum ada produk.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $products->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
