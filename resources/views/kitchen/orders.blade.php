@extends('layouts.app')

@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-6">Kitchen Display System</h1>

    <div class="mb-4 flex gap-2">
        <button onclick="filterOrders('all')" class="px-4 py-2 bg-blue-500 text-white rounded">Semua</button>
        <button onclick="filterOrders('pending')" class="px-4 py-2 bg-yellow-500 text-white rounded">Pending</button>
        <button onclick="filterOrders('processing')" class="px-4 py-2 bg-blue-400 text-white rounded">Processing</button>
        <button onclick="filterOrders('ready')" class="px-4 py-2 bg-green-500 text-white rounded">Ready</button>
        <button onclick="filterOrders('served')" class="px-4 py-2 bg-gray-500 text-white rounded">Served</button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($orders as $order)
        <div class="border rounded-lg p-4 shadow order-card" data-status="{{ $order->status }}">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="font-bold">Meja {{ $order->table->name ?? 'Takeaway' }}</h3>
                    <p class="text-sm text-gray-500">#{{ $order->sale->invoice_number }}</p>
                </div>
                <span class="px-2 py-1 rounded text-xs
                    {{ $order->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $order->status == 'processing' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $order->status == 'ready' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $order->status == 'served' ? 'bg-gray-100 text-gray-800' : '' }}">
                    {{ $order->status }}
                </span>
            </div>

            <div class="space-y-2">
                @foreach($order->items as $item)
                <div class="border-l-4 border-blue-500 pl-3 py-1 
                    {{ $item->status == 'ready' ? 'bg-green-50' : '' }}
                    {{ $item->status == 'served' ? 'bg-gray-50 opacity-60' : '' }}">
                    <div class="flex justify-between">
                        <span>{{ $item->saleItem->name_snapshot ?? 'Item' }} (x{{ $item->saleItem->qty ?? 1 }})</span>
                        @if($item->notes)
                            <span class="text-xs text-red-600">{{ $item->notes }}</span>
                        @endif
                    </div>
                    <span class="text-xs text-gray-500">{{ $item->status }}</span>
                </div>
                @endforeach
            </div>

            @if($order->notes)
                <div class="mt-2 p-2 bg-yellow-50 border-l-4 border-yellow-400">
                    <p class="text-sm"><strong>Catatan:</strong> {{ $order->notes }}</p>
                </div>
            @endif

            <div class="mt-4 flex gap-2">
                @if($order->status == 'pending')
                <form action="{{ route('kitchen.updateStatus', $order) }}" method="POST" class="flex-1">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="processing">
                    <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600">
                        Mulai Masak
                    </button>
                </form>
                @endif

                @if($order->status == 'processing')
                <form action="{{ route('kitchen.updateStatus', $order) }}" method="POST" class="flex-1">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="ready">
                    <button type="submit" class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-600">
                        Siap
                    </button>
                </form>
                @endif

                @if($order->status == 'ready')
                <form action="{{ route('kitchen.updateStatus', $order) }}" method="POST" class="flex-1">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="served">
                    <button type="submit" class="w-full bg-gray-500 text-white py-2 rounded hover:bg-gray-600">
                        Sudah Dikirim
                    </button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full text-center text-gray-500 py-8">
            Tidak ada order di dapur.
        </div>
        @endforelse
    </div>
</div>

<script>
function filterOrders(status) {
    document.querySelectorAll('.order-card').forEach(card => {
        if (status === 'all' || card.dataset.status === status) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}
</script>
@endsection
