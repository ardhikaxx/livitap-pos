@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item active">Kasir (POS)</li>
@endsection

@push('styles')
<style>
    #pos-area {
        height: calc(100vh - 64px);
        display: flex;
        overflow: hidden;
        background-color: #f3f4f6;
    }
    #pos-products { 
        flex: 1; 
        overflow-y: auto; 
        padding: 20px; 
    }
    #pos-cart { 
        width: 360px; 
        flex-shrink: 0; 
        display: flex; 
        flex-direction: column; 
        background: #ffffff; 
        border-left: 1px solid #e5e7eb; 
        box-shadow: -4px 0 15px rgba(0,0,0,0.02);
    }
    #pos-cart-items { 
        flex: 1; 
        overflow-y: auto; 
        padding: 16px; 
    }
    
    .category-pill {
        white-space: nowrap;
        padding: 0.5rem 1.25rem;
        border-radius: 2rem;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s;
        border: 1px solid #e5e7eb;
        background: #ffffff;
        color: #4b5563;
    }
    .category-pill:hover {
        background: #f9fafb;
        color: var(--primary-color);
        border-color: var(--primary-color);
    }
    .category-pill.active {
        background: var(--primary-color);
        color: #ffffff;
        border-color: var(--primary-color);
        box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
    }

    .product-card {
        background: #ffffff;
        border: 1px solid #f3f4f6;
        border-radius: 1rem;
        padding: 1rem;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        border-color: var(--primary-color);
    }
    .product-card .price {
        color: var(--primary-color);
        font-weight: 700;
        font-size: 1rem;
    }
    
    .cart-item {
        background: #f9fafb;
        border-radius: 0.75rem;
        padding: 12px;
        margin-bottom: 12px;
        border: 1px solid #f3f4f6;
        transition: all 0.2s;
    }
    .cart-item:hover {
        border-color: #e5e7eb;
        background: #ffffff;
    }

    #paid-amount::-webkit-inner-spin-button,
    #paid-amount::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    
    .receipt-modal-content {
        background: #ffffff;
        border-radius: 1.5rem;
        padding: 2rem;
        width: 100%;
        max-width: 440px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
</style>
@endpush

@section('full_content')
<div id="pos-area">

    {{-- Kolom 1: Grid Produk --}}
    <div id="pos-products">
        {{-- Baris atas: search + filter kategori --}}
        <div class="mb-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="product-search" oninput="filterProducts()" placeholder="Cari produk..." class="form-control border-start-0 ps-0 shadow-none">
                    </div>
                </div>
                <div class="col-md-8">
                    <div id="cat-tabs" class="d-flex gap-2 overflow-x-auto pb-2" style="scrollbar-width: none;">
                        <button class="category-pill active" onclick="filterCategory('all', this)">Semua</button>
                        @foreach(\App\Models\Category::all() as $category)
                        <button class="category-pill" onclick="filterCategory('{{ $category->id }}', this)">{{ $category->name }}</button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3" id="product-grid">
            @foreach($products as $product)
            @php $price = $product->prices->first()?->sell_price ?? 0; @endphp
            <div class="col-6 col-md-4 col-xl-3 product-item" data-category="{{ $product->category_id }}" data-name="{{ strtolower($product->name) }}">
                <div class="product-card" onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $price }})">
                    <div>
                        <div class="badge bg-light text-dark mb-2 fw-medium">{{ $product->category->name ?? 'Umum' }}</div>
                        <h6 class="fw-bold mb-1 text-dark line-clamp-2">{{ $product->name }}</h6>
                    </div>
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <span class="price">Rp {{ number_format($price, 0, ',', '.') }}</span>
                        <div class="btn btn-sm btn-light border rounded-circle p-1 d-flex align-items-center justify-content-center" style="width:28px; height:28px;">
                            <i class="bi bi-plus text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Kolom 3: Keranjang --}}
    <div id="pos-cart">
        <div class="p-4 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0">Keranjang</h5>
            <span class="badge bg-primary rounded-pill" id="cart-count">0 item</span>
        </div>

        <div id="pos-cart-items">
            <div id="cart-empty" class="text-center py-5">
                <div class="mb-3 text-muted opacity-25">
                    <i class="bi bi-cart-x" style="font-size: 4rem;"></i>
                </div>
                <p class="text-muted small">Keranjang masih kosong</p>
            </div>
        </div>

        <div class="p-4 bg-white border-top shadow-sm">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Subtotal</span>
                <span class="fw-medium" id="display-subtotal">Rp 0</span>
            </div>
            <div class="d-flex justify-content-between mb-4">
                <h5 class="fw-bold mb-0">Total</h5>
                <h5 class="fw-bold mb-0 text-primary" id="display-total">Rp 0</h5>
            </div>
            
            <div class="mb-4">
                <label class="form-label small fw-semibold text-muted text-uppercase mb-2">Jumlah Bayar</label>
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-light border-end-0">Rp</span>
                    <input type="number" id="paid-amount" oninput="calcChange()" placeholder="0" class="form-control bg-light border-start-0 shadow-none fw-bold" min="0">
                </div>
            </div>

            <div id="change-box" class="alert alert-success border-0 rounded-3 py-3 px-4 mb-4 d-flex justify-content-between align-items-center" style="display:none !important;">
                <span class="small fw-medium">Kembalian</span>
                <strong class="fs-5" id="display-change">Rp 0</strong>
            </div>

            <button id="btn-bayar" onclick="checkCustomer()" class="btn btn-primary w-100 py-3 fw-bold rounded-3">
                <i class="bi bi-check2-circle me-2"></i> SELESAIKAN PEMBAYARAN
            </button>
        </div>
    </div>

</div>

<!-- Modal Pelanggan -->
<div id="customer-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:10000; align-items:center; justify-content:center;">
    <div class="modal-dialog modal-dialog-centered" style="width:100%; max-width:400px; padding:20px;">
        <div class="modal-content bg-white p-4 rounded-4 shadow">
            <h5 class="mb-3">Identitas Pelanggan</h5>
            <input type="text" id="cust-name" class="form-control mb-3" placeholder="Masukkan nama pelanggan..." required>
            <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('customer-modal').style.display='none'">Batal</button>
                <button type="button" class="btn btn-primary" onclick="confirmCustomer()">OK</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Struk --}}
<div id="receipt-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(17, 24, 39, 0.7); backdrop-filter: blur(4px); z-index:9999; align-items:center; justify-content:center;">
    <div class="receipt-modal-content mx-3 animate__animated animate__zoomIn animate__faster">
        <div id="receipt-content">
            <div class="text-center mb-4">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:64px; height:64px;">
                    <i class="bi bi-check-lg fs-1"></i>
                </div>
                <h4 class="fw-bold mb-1">Pembayaran Berhasil!</h4>
                <p class="text-muted small">Terima kasih atas pembelian Anda</p>
            </div>
            
            <div class="bg-light rounded-4 p-4 mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">No. Transaksi</span>
                    <span class="fw-medium small" id="r-invoice"></span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted small">Tanggal</span>
                    <span class="fw-medium small" id="r-date"></span>
                </div>
                
                <div class="border-top border-2 border-dashed my-3"></div>
                
                <div id="r-items-container" class="mb-3">
                    <!-- Items injected here -->
                </div>
                
                <div class="border-top border-2 border-dashed my-3"></div>
                
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <span class="fw-bold">Total Pembayaran</span>
                    <span class="fw-bold text-primary fs-5" id="r-total"></span>
                </div>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-6">
                    <div class="p-3 bg-light rounded-3 text-center">
                        <div class="text-muted small mb-1">Bayar</div>
                        <div class="fw-bold" id="r-paid"></div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 bg-light rounded-3 text-center">
                        <div class="text-muted small mb-1">Kembali</div>
                        <div class="fw-bold text-success" id="r-change"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-3">
            <button onclick="printReceipt()" class="btn btn-outline-primary flex-fill py-2 fw-bold">
                <i class="bi bi-printer me-2"></i> Cetak Struk
            </button>
            <button onclick="newTransaction()" class="btn btn-primary flex-fill py-2 fw-bold">
                Transaksi Baru
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
var cart = [];
var activeCategory = 'all';
var cartTotal = 0;
var tempCustomerName = '';

function fmt(n) {
    return 'Rp ' + Number(n).toLocaleString('id-ID');
}

function filterCategory(cat, btn) {
    activeCategory = cat;
    document.querySelectorAll('.category-pill').forEach(function(b) {
        b.classList.remove('active');
    });
    btn.classList.add('active');
    filterProducts();
}

function filterProducts() {
    var search = document.getElementById('product-search').value.toLowerCase();
    document.querySelectorAll('.product-item').forEach(function(el) {
        var matchCat = activeCategory === 'all' || el.dataset.category === activeCategory;
        var matchSearch = !search || el.dataset.name.includes(search);
        el.style.display = (matchCat && matchSearch) ? '' : 'none';
    });
}

function addToCart(id, name, price) {
    var existing = cart.find(function(i) { return i.id === id; });
    if (existing) {
        existing.qty++;
    } else {
        cart.push({ id: id, name: name, price: price, qty: 1 });
    }
    renderCart();
}

function removeFromCart(id) {
    cart = cart.filter(function(i) { return i.id !== id; });
    renderCart();
}

function changeQty(id, val) {
    var item = cart.find(function(i) { return i.id === id; });
    if (!item) return;
    var qty = parseInt(val);
    if (qty < 1 || isNaN(qty)) qty = 1;
    item.qty = qty;
    renderCart();
}

function renderCart() {
    var container = document.getElementById('pos-cart-items');
    var empty = document.getElementById('cart-empty');
    var countBadge = document.getElementById('cart-count');

    // remove old rows
    container.querySelectorAll('.cart-item').forEach(function(el) { el.remove(); });

    var totalQty = 0;
    if (cart.length === 0) {
        empty.style.display = 'block';
        cartTotal = 0;
    } else {
        empty.style.display = 'none';
        cartTotal = 0;
        cart.forEach(function(item) {
            cartTotal += item.price * item.qty;
            totalQty += item.qty;
            var row = document.createElement('div');
            row.className = 'cart-item animate__animated animate__fadeIn';
            row.innerHTML =
                '<div class="d-flex justify-content-between align-items-start mb-2">'
                    + '<div class="overflow-hidden pe-2">'
                        + '<div class="fw-bold text-dark text-truncate" style="font-size: 0.9rem;">' + item.name + '</div>'
                        + '<div class="text-muted" style="font-size:0.75rem;">' + fmt(item.price) + '</div>'
                    + '</div>'
                    + '<button onclick="removeFromCart(' + item.id + ')" class="btn btn-sm text-danger p-0 border-0"><i class="bi bi-x-circle"></i></button>'
                + '</div>'
                + '<div class="d-flex justify-content-between align-items-center">'
                    + '<div class="input-group input-group-sm" style="width: 100px;">'
                        + '<button class="btn btn-outline-secondary py-0" onclick="changeQty(' + item.id + ', ' + (item.qty-1) + ')">-</button>'
                        + '<input type="number" value="' + item.qty + '" min="1" onchange="changeQty(' + item.id + ', this.value)" '
                            + 'class="form-control text-center p-0 shadow-none border-secondary border-opacity-25" readonly>'
                        + '<button class="btn btn-outline-secondary py-0" onclick="changeQty(' + item.id + ', ' + (item.qty+1) + ')">+</button>'
                    + '</div>'
                    + '<div class="fw-bold text-primary" style="font-size:0.9rem;">' + fmt(item.price * item.qty) + '</div>'
                + '</div>';
            container.appendChild(row);
        });
    }

    countBadge.textContent = totalQty + ' item';
    document.getElementById('display-subtotal').textContent = fmt(cartTotal);
    document.getElementById('display-total').textContent = fmt(cartTotal);
    calcChange();
    updateBayarBtn();
}

function calcChange() {
    var paid = parseFloat(document.getElementById('paid-amount').value) || 0;
    var change = paid - cartTotal;
    var box = document.getElementById('change-box');
    if (paid > 0 && change >= 0) {
        document.getElementById('display-change').textContent = fmt(change);
        box.style.setProperty('display', 'flex', 'important');
    } else {
        box.style.setProperty('display', 'none', 'important');
    }
    updateBayarBtn();
}

function updateBayarBtn() {
    var paid = parseFloat(document.getElementById('paid-amount').value) || 0;
    var btn = document.getElementById('btn-bayar');
    btn.disabled = cart.length === 0 || paid < cartTotal;
}

function checkCustomer() {
    document.getElementById('customer-modal').style.display = 'flex';
}

function confirmCustomer() {
    tempCustomerName = document.getElementById('cust-name').value;
    if(!tempCustomerName) return alert('Nama harus diisi!');
    document.getElementById('customer-modal').style.display = 'none';
    executePayment();
}

function executePayment() {
    var paid = parseFloat(document.getElementById('paid-amount').value) || 0;
    
    fetch('{{ route("pos.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            customer_name: tempCustomerName,
            outlet_id: {{ session('outlet_id', 1) }},
            items: cart.map(item => ({ product_id: item.id, qty: item.qty, price: item.price })),
            subtotal: cartTotal,
            total: cartTotal,
            paid_amount: paid,
            payment_method: 'cash'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            var change = Math.max(0, paid - cartTotal);
            var now = new Date();
            
            document.getElementById('r-invoice').textContent = data.data.invoice_number;
            document.getElementById('r-date').textContent = now.toLocaleString('id-ID');
            document.getElementById('r-total').textContent = fmt(cartTotal);
            document.getElementById('r-paid').textContent = fmt(paid);
            document.getElementById('r-change').textContent = fmt(change);
            
            var container = document.getElementById('r-items-container');
            container.innerHTML = '';
            cart.forEach(function(item) {
                var itemDiv = document.createElement('div');
                itemDiv.className = 'd-flex justify-content-between mb-2 small';
                itemDiv.innerHTML = '<div><span class="fw-medium">' + item.name + '</span><br><span class="text-muted">' + item.qty + ' x ' + fmt(item.price) + '</span></div>'
                    + '<div class="fw-bold">' + fmt(item.price * item.qty) + '</div>';
                container.appendChild(itemDiv);
            });
            
            document.getElementById('receipt-modal').style.display = 'flex';
        } else {
            alert('Gagal menyimpan transaksi: ' + data.message);
        }
    })
    .catch(error => { console.error('Error:', error); alert('Terjadi kesalahan.'); });
}

function printReceipt() {
    window.print();
}

function newTransaction() {
    cart = [];
    cartTotal = 0;
    document.getElementById('paid-amount').value = '';
    document.getElementById('receipt-modal').style.display = 'none';
    renderCart();
}
</script>
@endpush