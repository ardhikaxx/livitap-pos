@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item active">Dapur</li>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="card-title mb-4">Kitchen Display System</h5>

        <div class="d-flex gap-2 mb-4">
            <button onclick="filterOrders('all')" class="btn btn-primary btn-sm">Semua</button>
            <button onclick="filterOrders('pending')" class="btn btn-warning btn-sm">Pending</button>
            <button onclick="filterOrders('processing')" class="btn btn-info btn-sm">Processing</button>
            <button onclick="filterOrders('ready')" class="btn btn-success btn-sm">Ready</button>
            <button onclick="filterOrders('served')" class="btn btn-secondary btn-sm">Served</button>
        </div>

        <div class="row g-3">
            @forelse($orders as $order)
            <div class="col-md-4">
                <div class="card border order-card" data-status="{{ $order->status }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold mb-0">Meja {{ $order->table->name ?? 'Takeaway' }}</h6>
                                <small class="text-muted">#{{ $order->sale->invoice_number }}</small>
                            </div>
                            <span class="badge
                                {{ $order->status == 'pending' ? 'bg-warning text-dark' : '' }}
                                {{ $order->status == 'processing' ? 'bg-info' : '' }}
                                {{ $order->status == 'ready' ? 'bg-success' : '' }}
                                {{ $order->status == 'served' ? 'bg-secondary' : '' }}">
                                {{ $order->status }}
                            </span>
                        </div>

                        @foreach($order->items as $item)
                        <div class="border-start border-primary border-3 ps-2 py-1 mb-1 {{ $item->status == 'served' ? 'opacity-50' : '' }}">
                            <div class="d-flex justify-content-between">
                                <span class="small">{{ $item->saleItem->name_snapshot ?? 'Item' }} (x{{ $item->saleItem->qty ?? 1 }})</span>
                                @if($item->notes)
                                    <span class="small text-danger">{{ $item->notes }}</span>
                                @endif
                            </div>
                            <small class="text-muted">{{ $item->status }}</small>
                        </div>
                        @endforeach

                        @if($order->notes)
                        <div class="alert alert-warning py-1 px-2 mt-2 mb-2 small">
                            <strong>Catatan:</strong> {{ $order->notes }}
                        </div>
                        @endif

                        <div class="d-flex gap-2 mt-3">
                            @if($order->status == 'pending')
                            <form action="{{ route('kitchen.updateStatus', $order) }}" method="POST" class="flex-fill">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="processing">
                                <button type="submit" class="btn btn-primary btn-sm w-100">Mulai Masak</button>
                            </form>
                            @endif
                            @if($order->status == 'processing')
                            <form action="{{ route('kitchen.updateStatus', $order) }}" method="POST" class="flex-fill">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="ready">
                                <button type="submit" class="btn btn-success btn-sm w-100">Siap</button>
                            </form>
                            @endif
                            @if($order->status == 'ready')
                            <form action="{{ route('kitchen.updateStatus', $order) }}" method="POST" class="flex-fill">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="served">
                                <button type="submit" class="btn btn-secondary btn-sm w-100">Sudah Dikirim</button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center text-muted py-4">Tidak ada order di dapur.</div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
function filterOrders(status) {
    document.querySelectorAll('.order-card').forEach(card => {
        card.closest('.col-md-4').style.display = (status === 'all' || card.dataset.status === status) ? '' : 'none';
    });
}
</script>
@endpush
@endsection
