@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('stocks.index') }}">Stok</a></li>
<li class="breadcrumb-item active">Adjust: {{ $product->name }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-4">Penyesuaian Stok</h5>

                <table class="table table-sm mb-4">
                    <tr><th style="width:140px">Produk</th><td>{{ $product->name }}</td></tr>
                    <tr><th>SKU</th><td>{{ $product->sku }}</td></tr>
                    <tr><th>Stok Saat Ini</th><td><strong>{{ $stock?->qty ?? 0 }}</strong> {{ $product->unit }}</td></tr>
                </table>

                <form method="POST" action="{{ route('stocks.update', $product) }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-medium">Tindakan</label>
                        <select name="action" id="action" class="form-select" onchange="updateLabel()">
                            <option value="add">Tambah Stok</option>
                            <option value="subtract">Kurangi Stok</option>
                            <option value="set">Set Stok (langsung)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium" id="qty-label">Jumlah Tambah</label>
                        <input type="number" name="qty" class="form-control @error('qty') is-invalid @enderror"
                            min="0" step="0.01" value="{{ old('qty', 0) }}" required>
                        @error('qty')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">Catatan (opsional)</label>
                        <input type="text" name="notes" class="form-control" value="{{ old('notes') }}"
                            placeholder="Alasan penyesuaian stok...">
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('stocks.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Penyesuaian</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateLabel() {
    var action = document.getElementById('action').value;
    var labels = { add: 'Jumlah Tambah', subtract: 'Jumlah Kurangi', set: 'Set Stok Menjadi' };
    document.getElementById('qty-label').textContent = labels[action];
}
</script>
@endpush
