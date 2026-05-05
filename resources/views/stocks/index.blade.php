@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item active">Stok</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="card-title mb-4">Stok Produk</h5>

        <form method="GET" class="row g-2 mb-4">
            <div class="col-md-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..." class="form-control form-control-sm">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-secondary btn-sm">Cari</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Produk</th>
                        <th class="text-end">Stok</th>
                        <th class="text-end">Min</th>
                        <th class="text-end">Max</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stocks as $stock)
                    <tr>
                        <td>{{ $stock->product->name }}</td>
                        <td class="text-end">{{ $stock->qty }}</td>
                        <td class="text-end">{{ $stock->min_qty }}</td>
                        <td class="text-end">{{ $stock->max_qty ?? '-' }}</td>
                        <td class="text-center">
                            @if($stock->qty <= $stock->min_qty && $stock->min_qty > 0)
                                <span class="badge bg-danger">LOW STOCK</span>
                            @else
                                <span class="badge bg-success">OK</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('stocks.adjust', $stock->product) }}" class="btn btn-outline-primary btn-sm">Adjust</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Belum ada data stok.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $stocks->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
