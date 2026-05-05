@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item active">Kasir (POS)</li>
@endsection

@push('styles')
<style>
    #pos-wrapper { height: calc(100vh - 53px); overflow: hidden; }
    #product-grid { overflow-y: auto; }
    #cart-panel { overflow-y: auto; }
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<div id="pos-wrapper" x-data="posApp()" class="d-flex gap-0 p-0 m-0" style="margin: -1.5rem !important;">

    {{-- Category Sidebar --}}
    <div class="bg-dark text-white d-flex flex-column flex-shrink-0" style="width:180px; overflow-y:auto;">
        <div class="p-2 border-bottom border-secondary">
            <small class="text-secondary text-uppercase fw-bold" style="font-size:0.65rem;">Kategori</small>
        </div>
        <div class="p-2">
            <button @click="activeCategory = 'all'"
                :class="activeCategory === 'all' ? 'btn-primary' : 'btn-outline-secondary'"
                class="btn btn-sm w-100 text-start mb-1">Semua</button>
            @foreach(\App\Models\Category::all() as $category)
                <button @click="activeCategory = '{{ $category->id }}'"
                    :class="activeCategory == '{{ $category->id }}' ? 'btn-primary' : 'btn-outline-secondary'"
                    class="btn btn-sm w-100 text-start mb-1">{{ $category->name }}</button>
            @endforeach
        </div>
    </div>

    {{-- Product Grid --}}
    <div id="product-grid" class="flex-grow-1 bg-secondary bg-opacity-10 p-3">
        <input type="text" x-model="search" placeholder="🔍 Cari produk..." class="form-control form-control-sm mb-3">
        <div class="row g-2">
            @foreach(\App\Models\Product::with('prices')->where('is_active', true)->get() as $product)
                @php $price = $product->prices->first()?->sell_price ?? 0; @endphp
                <div class="col-6 col-md-4 col-lg-3"
                    data-category="{{ $product->category_id }}"
                    :class="{'d-none': activeCategory != 'all' && activeCategory != {{ $product->category_id }}, 'd-none': search && !'{{ strtolower($product->name) }}'.includes(search.toLowerCase())}">
                    <button @click="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $price }})"
                        class="btn btn-dark w-100 h-100 text-start p-2 border">
                        <div class="fw-medium small">{{ $product->name }}</div>
                        <div class="text-primary fw-bold small mt-1">Rp {{ number_format($price, 0, ',', '.') }}</div>
                    </button>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Cart Panel --}}
    <div id="cart-panel" class="bg-white border-start d-flex flex-column flex-shrink-0" style="width:320px;">
        <div class="p-3 border-bottom">
            <h6 class="fw-bold mb-0">🛒 Keranjang</h6>
        </div>

        <div class="flex-grow-1 overflow-auto p-2">
            <template x-for="(item, index) in cart" :key="index">
                <div class="d-flex align-items-center border-bottom py-2 gap-2">
                    <div class="flex-grow-1">
                        <div class="small fw-medium" x-text="item.name"></div>
                        <div class="text-muted" style="font-size:0.75rem;">Rp <span x-text="item.price.toLocaleString('id-ID')"></span></div>
                    </div>
                    <input type="number" x-model="item.qty" @input="updateTotal()" min="1"
                        class="form-control form-control-sm text-center" style="width:55px;">
                    <div class="text-end" style="min-width:70px;">
                        <small class="fw-bold">Rp <span x-text="(item.price * item.qty).toLocaleString('id-ID')"></span></small>
                    </div>
                    <button @click="removeItem(index)" class="btn btn-sm btn-outline-danger px-1 py-0">✕</button>
                </div>
            </template>
            <div x-show="cart.length === 0" class="text-center text-muted py-4 small">Keranjang kosong</div>
        </div>

        <div class="p-3 border-top bg-light">
            <div class="d-flex justify-content-between fw-bold mb-2">
                <span>Total</span>
                <span class="text-primary">Rp <span x-text="total.toLocaleString('id-ID')"></span></span>
            </div>
            <div class="mb-2">
                <label class="form-label small mb-1">Jumlah Bayar</label>
                <input type="number" x-model="paidAmount" @input="calculateChange()" placeholder="0"
                    class="form-control form-control-sm">
            </div>
            <div x-show="changeAmount > 0" class="alert alert-success py-1 px-2 small mb-2">
                Kembalian: Rp <span x-text="changeAmount.toLocaleString('id-ID')"></span>
            </div>
            <button @click="showReceiptModal = true"
                :disabled="cart.length === 0 || paidAmount < total"
                class="btn btn-success w-100 fw-bold">BAYAR</button>
        </div>
    </div>

    {{-- Receipt Modal --}}
    <div x-show="showReceiptModal" x-cloak
        class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
        style="background:rgba(0,0,0,0.5); z-index:9999;">
        <div class="bg-white rounded shadow p-4" style="width:100%;max-width:420px;">
            <div id="receipt-content">
                <div class="text-center mb-3">
                    <h5 class="fw-bold mb-0">LIVITAP POS</h5>
                    <small class="text-muted">Jl. Sudirman No. 123, Jakarta</small>
                </div>
                <hr>
                <div class="d-flex justify-content-between small mb-1">
                    <span>No. Struk:</span><span x-text="receiptData.invoice"></span>
                </div>
                <div class="d-flex justify-content-between small mb-2">
                    <span>Tanggal:</span><span x-text="receiptData.date"></span>
                </div>
                <hr>
                <table class="table table-sm table-borderless mb-2">
                    <thead><tr><th>Item</th><th class="text-end">Subtotal</th></tr></thead>
                    <tbody>
                        <template x-for="item in cart" :key="item.id">
                            <tr>
                                <td>
                                    <span x-text="item.name"></span><br>
                                    <small class="text-muted"><span x-text="item.qty"></span> x Rp <span x-text="item.price.toLocaleString('id-ID')"></span></small>
                                </td>
                                <td class="text-end">Rp <span x-text="(item.price * item.qty).toLocaleString('id-ID')"></span></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <hr>
                <div class="d-flex justify-content-between fw-bold"><span>Total:</span><span>Rp <span x-text="total.toLocaleString('id-ID')"></span></span></div>
                <div class="d-flex justify-content-between small"><span>Bayar:</span><span>Rp <span x-text="Number(paidAmount).toLocaleString('id-ID')"></span></span></div>
                <div class="d-flex justify-content-between small"><span>Kembali:</span><span>Rp <span x-text="changeAmount.toLocaleString('id-ID')"></span></span></div>
                <div class="text-center mt-3 small text-muted">Terima kasih atas kunjungan Anda!</div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button @click="printReceipt()" class="btn btn-primary flex-fill">Print</button>
                <button @click="newTransaction()" class="btn btn-secondary flex-fill">Transaksi Baru</button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
                if (existing) { existing.qty++; }
                else { this.cart.push({id, name, price, qty: 1}); }
                this.updateTotal();
            },

            removeItem(index) {
                this.cart.splice(index, 1);
                this.updateTotal();
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
                if (!win) { alert('Popup diblokir! Izinkan popup untuk mencetak.'); return; }
                win.document.write(`<html><head><title>Struk</title><style>
                    @page{size:80mm auto;margin:0}
                    body{font-family:'Courier New',monospace;font-size:12px;width:72mm;margin:0;padding:2mm;color:#000;line-height:1.2}
                    .text-center{text-align:center}.text-end{text-align:right}
                    .fw-bold{font-weight:bold}.small{font-size:10px}
                    table{width:100%}th,td{padding:2px 0}
                    hr{border:none;border-top:1px dashed #000;margin:4px 0}
                    .d-flex{display:flex}.justify-content-between{justify-content:space-between}
                </style></head><body>${printContent}</body></html>`);
                win.document.close();
                setTimeout(() => { win.focus(); win.print(); win.close(); }, 500);
            },

            newTransaction() {
                this.cart = [];
                this.paidAmount = 0;
                this.changeAmount = 0;
                this.total = 0;
                this.showReceiptModal = false;
                this.receiptData.invoice = 'INV-' + new Date().toISOString().slice(11,19).replace(/:/g,'');
                this.receiptData.date = new Date().toLocaleString('id-ID');
            }
        }
    }
</script>
@endpush
