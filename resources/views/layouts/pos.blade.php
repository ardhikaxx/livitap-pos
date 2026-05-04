<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS - LIVITAP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('styles')
</head>
<body class="bg-gray-900 text-white h-screen overflow-hidden">
    <div x-data="{ cart: [], search: '', activeCategory: 'all' }" class="flex h-full">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 p-4 overflow-y-auto">
            <h2 class="font-bold text-lg mb-4">Kategori</h2>
            <button @click="activeCategory = 'all'" :class="{'bg-blue-600': activeCategory === 'all'}" class="block w-full text-left p-2 rounded hover:bg-gray-700 mb-1">Semua Produk</button>
            @foreach(\App\Models\Category::all() as $category)
                <button @click="activeCategory = '{{ $category->id }}'" :class="{'bg-blue-600': activeCategory == '{{ $category->id }}'}" class="block w-full text-left p-2 rounded hover:bg-gray-700 mb-1">{{ $category->name }}</button>
            @endforeach
        </div>

        <!-- Product Grid -->
        <div class="flex-1 p-4 overflow-y-auto">
            <div class="mb-4">
                <input type="text" x-model="search" placeholder="Cari produk..." class="w-full p-3 rounded bg-gray-800 text-white placeholder-gray-400">
            </div>
            <div class="grid grid-cols-4 gap-3">
                @foreach(\App\Models\Product::with('prices')->where('is_active', true)->get() as $product)
                    @php $price = $product->prices->first()?->sell_price ?? 0; @endphp
                    <button @click="cart.push({id: {{ $product->id }}, name: '{{ $product->name }}', price: {{ $price }}, qty: 1}); $dispatch('cart-updated')" 
                            data-category="{{ $product->category_id }}"
                            :class="{'hidden': activeCategory != 'all' && activeCategory != {{ $product->category_id }}}"
                            class="bg-gray-800 p-3 rounded hover:bg-gray-700 text-center">
                        <div class="font-medium">{{ $product->name }}</div>
                        <div class="text-blue-400 font-bold mt-1">Rp {{ number_format($price, 0, ',', '.') }}</div>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Cart Section -->
        <div class="w-96 bg-gray-800 p-4 flex flex-col">
            <h2 class="font-bold text-lg mb-4">Keranjang</h2>
            <div class="flex-1 overflow-y-auto mb-4">
                <template x-for="(item, index) in cart" :key="index">
                    <div class="flex justify-between items-center py-2 border-b border-gray-700">
                        <div>
                            <div x-text="item.name"></div>
                            <div class="text-sm text-gray-400">Rp <span x-text="item.price.toLocaleString()"></span> x <span x-text="item.qty"></span></div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold">Rp <span x-text="(item.price * item.qty).toLocaleString()"></span></div>
                            <button @click="cart.splice(index, 1)" class="text-red-400 text-sm">Hapus</button>
                        </div>
                    </div>
                </template>
            </div>
            
            <div class="border-t border-gray-700 pt-4">
                <div class="text-xl font-bold mb-4">Total: Rp <span x-text="cart.reduce((sum, item) => sum + (item.price * item.qty), 0).toLocaleString()"></span></div>
                <form action="{{ route('pos.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="outlet_id" value="1">
                    <input type="hidden" name="items" :value="JSON.stringify(cart)">
                    <input type="number" name="paid_amount" placeholder="Jumlah Bayar" class="w-full p-3 rounded bg-gray-700 text-white" required>
                    <button type="submit" class="w-full bg-green-600 p-3 rounded font-bold hover:bg-green-700">BAYAR</button>
                </form>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>