@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item active">Produk</li>
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Manajemen Produk</h4>
        <p class="text-muted small mb-0">Kelola inventaris produk Anda di satu tempat.</p>
    </div>
    <a href="{{ route('products.create') }}" class="btn btn-primary fw-bold shadow-sm px-4">
        <i class="bi bi-plus-lg me-2"></i> Tambah Produk
    </a>
</div>

<div class="card border-0 shadow-sm mb-4 rounded-3">
    <div class="card-body p-4">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label small fw-bold text-uppercase text-muted">Pencarian</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau SKU..." class="form-control form-control-lg shadow-none border-light">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-uppercase text-muted">Kategori</label>
                <select name="category_id" class="form-select form-select-lg shadow-none border-light">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-uppercase text-muted">Status</label>
                <select name="is_active" class="form-select form-select-lg shadow-none border-light">
                    <option value="">Semua</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-light btn-lg w-100 fw-bold border-light">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="text-uppercase small text-muted bg-light">
                    <tr>
                        <th class="px-4 py-3 border-0">Nama Produk</th>
                        <th class="px-4 py-3 border-0">Kategori</th>
                        <th class="px-4 py-3 border-0">SKU</th>
                        <th class="px-4 py-3 border-0 text-end">Harga Jual</th>
                        <th class="px-4 py-3 border-0 text-end">Stok</th>
                        <th class="px-4 py-3 border-0 text-center">Status</th>
                        <th class="px-4 py-3 border-0 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td class="px-4 py-3 fw-bold text-dark">
                            <a href="{{ route('products.show', $product) }}" class="text-decoration-none text-dark">{{ $product->name }}</a>
                        </td>
                        <td class="px-4 py-3 text-muted">{{ $product->category->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-monospace text-muted">{{ $product->sku }}</td>
                        <td class="px-4 py-3 text-end fw-bold text-primary">Rp {{ number_format($product->prices->first()?->sell_price ?? 0, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-end fw-bold">
                            {{ $product->stocks->sum('qty') ?? 0 }}
                            @if($product->track_stock && ($product->stocks->sum('qty') <= ($product->stocks->first()?->min_qty ?? 0)))
                                <span class="badge bg-danger rounded-pill ms-2 small">Low</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }} rounded-pill px-3 py-2">
                                {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-end">
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-light border-0 fw-bold me-2">Edit</a>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light border-0 fw-bold text-danger" onclick="return confirm('Hapus produk?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">Belum ada produk.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-top">{{ $products->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
