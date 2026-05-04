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
            <h2 class="font-bold text-lg mb-4">LIVITAP POS</h2>
            
            <h3 class="font-bold text-sm text-gray-500 mb-2 mt-4">Navigasi Utama</h3>
            <a href="{{ route('products.index') }}" class="block w-full text-left p-2 rounded hover:bg-gray-700 mb-1 text-white text-decoration-none">Produk</a>
            <a href="{{ route('stocks.index') }}" class="block w-full text-left p-2 rounded hover:bg-gray-700 mb-1 text-white text-decoration-none">Stok</a>
            <a href="{{ route('reports.sales') }}" class="block w-full text-left p-2 rounded hover:bg-gray-700 mb-1 text-white text-decoration-none">Laporan</a>
            <a href="{{ route('settings.business') }}" class="block w-full text-left p-2 rounded hover:bg-gray-700 mb-1 text-white text-decoration-none">Pengaturan</a>
            <hr class="my-3 border-gray-600">

            <h3 class="font-bold text-sm text-gray-500 mb-2">Kategori Produk</h3>
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
                
                <button @click="showReceiptModal = true" class="w-full bg-green-600 p-3 rounded font-bold hover:bg-green-700" :disabled="cart.length === 0 || paidAmount < total">BAYAR</button>
            </div>
        </div>

        <!-- Receipt Modal -->
        <div x-show="showReceiptModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white text-black rounded-lg p-6 w-full max-w-md mx-4">
                <div id="receipt-content">
                    <div class="text-center mb-4">
                        <h2 class="font-bold text-lg">LIVITAP POS</h2>
                        <p class="text-sm">Jl. Sudirman No. 123, Jakarta</p>
                    </div>
                    
                    <div class="border-t border-b py-2 mb-2">
                        <div class="flex justify-between text-sm">
                            <span>No. Struk:</span>
                            <span x-text="receiptData.invoice"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>Tanggal:</span>
                            <span x-text="receiptData.date"></span>
                        </div>
                    </div>
                    
                    <table class="w-full text-sm mb-4">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left">Item</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="item in cart" :key="item.id">
                                <tr>
                                    <td>
                                        <span x-text="item.name"></span><br>
                                        <span class="text-xs"><span x-text="item.qty"></span> x Rp <span x-text="item.price.toLocaleString()"></span></span>
                                    </td>
                                    <td class="text-right">Rp <span x-text="(item.price * item.qty).toLocaleString()"></span></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    
                    <div class="border-t pt-2">
                        <div class="flex justify-between font-bold">
                            <span>Total:</span>
                            <span>Rp <span x-text="total.toLocaleString()"></span></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Bayar:</span>
                            <span>Rp <span x-text="paidAmount.toLocaleString()"></span></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Kembali:</span>
                            <span>Rp <span x-text="changeAmount.toLocaleString()"></span></span>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4 text-xs">
                        <p>Terima kasih atas kunjungan Anda!</p>
                    </div>
                </div>
                
                <div class="flex space-x-2 mt-4">
                    <button @click="printReceipt" class="flex-1 bg-blue-600 text-white py-2 rounded">Print</button>
                    <button @click="newTransaction" class="flex-1 bg-gray-600 text-white py-2 rounded">Transaksi Baru</button>
                </div>
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
                showReceiptModal: false,
                receiptData: {
                    invoice: 'INV-' + new Date().toISOString().slice(11,19).replace(/:/g,''),
                    date: new Date().toLocaleString('id-ID')
                },
                
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
                },
                
                printReceipt() {
                    const printContent = document.getElementById('receipt-content').innerHTML;
                    const win = window.open('', '_blank', 'width=300,height=600');
                    
                    if (!win) {
                        alert('Popup diblokir! Izinkan popup untuk mencetak.');
                        return;
                    }

                    win.document.write(`
                        <html>
                        <head>
                            <title>Print Struk</title>
                            <style>
                                @page { size: 80mm auto; margin: 0; }
                                body { font-family: 'Courier New', Courier, monospace; font-size: 12px; width: 72mm; margin: 0; padding: 2mm; color: #000; line-height: 1.1; }
                                .text-center { text-align: center; }
                                .text-right { text-align: right; }
                                .mb-1 { margin-bottom: 2px; }
                                .mb-2 { margin-bottom: 5px; }
                                .mb-4 { margin-bottom: 10px; }
                                .font-bold { font-weight: bold; }
                                .text-lg { font-size: 14px; }
                                .border-t { border-top: 1px dashed #000; padding-top: 3px; }
                                .border-b { border-bottom: 1px dashed #000; padding-bottom: 3px; }
                                .py-1 { padding: 3px 0; }
                                .flex { display: flex; }
                                .justify-between { justify-content: space-between; }
                                .w-full { width: 100%; }
                                .header { text-align: center; margin-bottom: 8px; border-bottom: 1px solid #000; padding-bottom: 5px; }
                                .header h2 { font-size: 16px; margin: 0; }
                                .table-header { display: flex; font-weight: bold; border-bottom: 1px dashed #000; padding-bottom: 2px; margin-bottom: 3px; }
                                .item-row { display: flex; justify-content: space-between; margin-bottom: 2px; }
                                .item-name { flex: 2; word-break: break-all; padding-right: 5px; }
                                .item-qty { flex: 0.5; text-align: center; }
                                .item-price { flex: 1; text-align: right; }
                                .total-section { margin-top: 5px; }
                                .footer { margin-top: 8px; text-align: center; font-size: 10px; }
                            </style>
                        </head>
                        <body>${printContent}</body>
                        </html>
                    `);
                    
                    win.document.close();
                    
                    setTimeout(() => {
                        win.focus();
                        win.print();
                        win.close();
                    }, 500);
                },
                
                newTransaction() {
                    this.cart = [];
                    this.paidAmount = 0;
                    this.changeAmount = 0;
                    this.total = 0;
                    this.showReceiptModal = false;
                }
            }
        }
    </script>
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</body>
</html>