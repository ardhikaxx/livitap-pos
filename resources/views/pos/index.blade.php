<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS - LIVITAP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-900 text-white h-screen overflow-hidden">
    <div x-data="posApp()" class="flex h-full">
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
                    <button @click="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $price }})" 
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
                        <div class="flex-1">
                            <div x-text="item.name"></div>
                            <div class="text-sm text-gray-400">Rp <span x-text="item.price.toLocaleString()"></span></div>
                        </div>
                        <div class="flex items-center">
                            <input type="number" x-model="item.qty" @input="updateTotal()" min="1" class="w-16 p-1 text-black rounded mr-2">
                            <div class="text-right w-20">
                                <div class="font-bold">Rp <span x-text="(item.price * item.qty).toLocaleString()"></span></div>
                            </div>
                            <button @click="removeItem(index); updateTotal()" class="text-red-400 text-sm ml-2">X</button>
                        </div>
                    </div>
                </template>
            </div>
            
            <div class="border-t border-gray-700 pt-4">
                <div class="text-xl font-bold mb-2">Total: Rp <span x-text="total.toLocaleString()"></span></div>
                
                <div class="mb-4">
                    <label class="block mb-1">Jumlah Bayar:</label>
                    <input type="number" x-model="paidAmount" @input="calculateChange()" placeholder="0" class="w-full p-3 rounded bg-gray-700 text-white">
                </div>
                
                <div class="text-lg font-bold mb-4 text-green-400" x-show="changeAmount > 0">
                    Kembalian: Rp <span x-text="changeAmount.toLocaleString()"></span>
                </div>
                
                <form action="{{ route('pos.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="outlet_id" value="1">
                    <input type="hidden" name="items" :value="JSON.stringify(cart)">
                    <input type="hidden" name="paid_amount" :value="paidAmount">
                    <button type="submit" class="w-full bg-green-600 p-3 rounded font-bold hover:bg-green-700" :disabled="cart.length === 0 || paidAmount < total">BAYAR</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function posApp() {
            return {
                cart: [],
                search: '',
                activeCategory: 'all',
                total: 0,
                paidAmount: 0,
                changeAmount: 0,
                
                addToCart(id, name, price) {
                    const existing = this.cart.find(i => i.id === id);
                    if (existing) {
                        existing.qty++;
                    } else {
                        this.cart.push({id, name, price, qty: 1});
                    }
                    this.updateTotal();
                },
                
                removeItem(index) {
                    this.cart.splice(index, 1);
                },
                
                updateTotal() {
                    this.total = this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
                    this.calculateChange();
                },
                
                calculateChange() {
                    this.changeAmount = Math.max(0, this.paidAmount - this.total);
                }
            }
        }
    </script>
</body>
</html>