<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>POS - Livitap</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 p-6" x-data="{ cart: [], total: 0 }">
    <h1 class="text-2xl font-bold mb-4">Kasir POS</h1>
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white p-4 shadow">
            <h2 class="font-bold mb-2">Produk</h2>
            @foreach(\App\Models\Product::with('prices')->get() as $product)
                @php $price = $product->prices->first()?->sell_price ?? 0; @endphp
                <button @click="cart.push({id: {{ $product->id }}, name: '{{ $product->name }}', price: {{ $price }}}); total += {{ $price }}" class="block w-full border-b py-2 text-left hover:bg-gray-50">
                    {{ $product->name }} - Rp{{ number_format($price, 0, ',', '.') }}
                </button>
            @endforeach
        </div>
        <div class="bg-white p-4 shadow">
            <h2 class="font-bold mb-2">Keranjang</h2>
            <template x-for="(item, index) in cart" :key="index">
                <div class="flex justify-between py-1">
                    <span x-text="item.name"></span>
                    <span x-text="'Rp' + item.price.toLocaleString()"></span>
                </div>
            </template>
            <div class="font-bold text-xl mt-4">Total: <span x-text="'Rp' + total.toLocaleString()"></span></div>
            <form action="{{ route('pos.store') }}" method="POST" class="mt-4">
                @csrf
                <input type="hidden" name="outlet_id" value="1">
                <input type="number" name="paid_amount" placeholder="Bayar" class="border p-2 w-full mb-2" required>
                <button type="submit" class="bg-blue-600 text-white p-2 w-full">Bayar</button>
            </form>
        </div>
    </div>
</body>
</html>
