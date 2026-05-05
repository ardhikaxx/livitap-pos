@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item active">Kasir (POS)</li>
@endsection

@push('styles')
<style>
    #pos-area {
        height: calc(100vh - 53px);
        display: flex;
        overflow: hidden;
    }
    #pos-products   { flex: 1; overflow-y: auto; background:#f8f9fa; padding:12px; }
    #pos-cart       { width: 310px; flex-shrink: 0; display: flex; flex-direction: column; overflow: hidden; background:#fff; border-left:1px solid #dee2e6; }
    #pos-cart-items { flex: 1; overflow-y: auto; padding:8px; }
    .product-btn    { background:#fff; border:1px solid #dee2e6; border-radius:6px; padding:8px; width:100%; text-align:left; cursor:pointer; transition:background .15s; }
    .product-btn:hover { background:#e9ecef; }
    #cat-tabs .btn  { font-size:0.8rem; }
</style>
@endpush

@section('full_content')
<div id="pos-area">

    {{-- Kolom 1: Grid Produk --}}
    <div id="pos-products">
        {{-- Baris atas: search + filter kategori --}}
        <div class="d-flex gap-2 mb-3 flex-wrap align-items-center">
            <input type="text" id="product-search" oninput="filterProducts()" placeholder="Cari produk..." class="form-control form-control-sm" style="max-width:220px;">
            <div id="cat-tabs" class="d-flex gap-1 flex-wrap">
                <button class="btn btn-primary btn-sm" onclick="filterCategory('all', this)">Semua</button>
                @foreach(\App\Models\Category::all() as $category)
                <button class="btn btn-outline-secondary btn-sm" onclick="filterCategory('{{ $category->id }}', this)">{{ $category->name }}</button>
                @endforeach
            </div>
        </div>
        <div class="row g-2" id="product-grid">
            @foreach($products as $product)
            @php $price = $product->prices->first()?->sell_price ?? 0; @endphp
            <div class="col-6 col-xl-4 product-item" data-category="{{ $product->category_id }}" data-name="{{ strtolower($product->name) }}">
                <button class="product-btn" onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $price }})">
                    <div class="fw-semibold small">{{ $product->name }}</div>
                    <div class="text-primary fw-bold mt-1" style="font-size:0.8rem;">Rp {{ number_format($price, 0, ',', '.') }}</div>
                </button>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Kolom 3: Keranjang --}}
    <div id="pos-cart">
        <div class="p-3 border-bottom">
            <h6 class="fw-bold mb-0">🛒 Keranjang</h6>
        </div>

        <div id="pos-cart-items">
            <div id="cart-empty" class="text-center text-muted py-5 small">Belum ada item</div>
        </div>

        <div class="p-3 border-top bg-light">
            <div class="d-flex justify-content-between fw-bold mb-3">
                <span>Total</span>
                <span class="text-primary" id="display-total">Rp 0</span>
            </div>
            <div class="mb-2">
                <label class="form-label small mb-1 fw-medium">Jumlah Bayar (Rp)</label>
                <input type="number" id="paid-amount" oninput="calcChange()" placeholder="0" class="form-control form-control-sm" min="0">
            </div>
            <div id="change-box" class="alert alert-success py-1 px-2 mb-2 small" style="display:none;">
                Kembalian: <strong id="display-change">Rp 0</strong>
            </div>
            <button id="btn-bayar" onclick="openReceiptModal()" disabled class="btn btn-success w-100 fw-bold">BAYAR</button>
        </div>
    </div>

</div>

{{-- Modal Struk — di luar #pos-area agar tidak terpengaruh overflow:hidden --}}
<div id="receipt-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:8px; padding:24px; width:100%; max-width:400px; max-height:90vh; overflow-y:auto; margin:auto; position:relative; top:50%; transform:translateY(-50%);">
        <div id="receipt-content">
            <div class="text-center mb-3">
                <h5 class="fw-bold mb-0">LIVITAP POS</h5>
                <small class="text-muted">Jl. Sudirman No. 123, Jakarta</small>
            </div>
            <hr>
            <div class="d-flex justify-content-between small"><span>No. Struk:</span><span id="r-invoice"></span></div>
            <div class="d-flex justify-content-between small"><span>Tanggal:</span><span id="r-date"></span></div>
            <hr>
            <table class="table table-sm table-borderless mb-0">
                <thead><tr><th class="ps-0">Item</th><th class="text-end pe-0">Subtotal</th></tr></thead>
                <tbody id="r-items"></tbody>
            </table>
            <hr>
            <div class="d-flex justify-content-between fw-bold"><span>Total:</span><span id="r-total"></span></div>
            <div class="d-flex justify-content-between small text-muted"><span>Bayar:</span><span id="r-paid"></span></div>
            <div class="d-flex justify-content-between small text-muted"><span>Kembali:</span><span id="r-change"></span></div>
            <div class="text-center mt-3 small text-muted">Terima kasih atas kunjungan Anda!</div>
        </div>
        <div class="d-flex gap-2 mt-3">
            <button onclick="printReceipt()" class="btn btn-primary flex-fill">🖨️ Print</button>
            <button onclick="newTransaction()" class="btn btn-secondary flex-fill">Transaksi Baru</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
var cart = [];
var activeCategory = 'all';
var cartTotal = 0;

function fmt(n) {
    return 'Rp ' + Number(n).toLocaleString('id-ID');
}

function filterCategory(cat, btn) {
    activeCategory = cat;
    document.querySelectorAll('#cat-tabs .btn').forEach(function(b) {
        b.classList.remove('btn-primary');
        b.classList.add('btn-outline-secondary');
    });
    btn.classList.remove('btn-outline-secondary');
    btn.classList.add('btn-primary');
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

    // remove old rows
    container.querySelectorAll('.cart-row').forEach(function(el) { el.remove(); });

    if (cart.length === 0) {
        empty.style.display = '';
        cartTotal = 0;
    } else {
        empty.style.display = 'none';
        cartTotal = 0;
        cart.forEach(function(item) {
            cartTotal += item.price * item.qty;
            var row = document.createElement('div');
            row.className = 'cart-row d-flex align-items-center gap-2 border-bottom py-2';
            row.innerHTML =
                '<div class="flex-grow-1 overflow-hidden">'
                    + '<div class="small fw-semibold text-truncate">' + item.name + '</div>'
                    + '<div class="text-muted" style="font-size:0.72rem;">' + fmt(item.price) + '</div>'
                + '</div>'
                + '<input type="number" value="' + item.qty + '" min="1" onchange="changeQty(' + item.id + ', this.value)" '
                    + 'class="form-control form-control-sm text-center p-1" style="width:50px;">'
                + '<div class="text-end" style="min-width:65px;font-size:0.78rem;">'
                    + '<span class="fw-bold">' + fmt(item.price * item.qty) + '</span>'
                + '</div>'
                + '<button onclick="removeFromCart(' + item.id + ')" class="btn btn-sm btn-outline-danger lh-1 p-1">✕</button>';
            container.appendChild(row);
        });
    }

    document.getElementById('display-total').textContent = fmt(cartTotal);
    calcChange();
    updateBayarBtn();
}

function calcChange() {
    var paid = parseFloat(document.getElementById('paid-amount').value) || 0;
    var change = paid - cartTotal;
    var box = document.getElementById('change-box');
    if (change > 0) {
        document.getElementById('display-change').textContent = fmt(change);
        box.style.display = '';
    } else {
        box.style.display = 'none';
    }
    updateBayarBtn();
}

function updateBayarBtn() {
    var paid = parseFloat(document.getElementById('paid-amount').value) || 0;
    var btn = document.getElementById('btn-bayar');
    btn.disabled = cart.length === 0 || paid < cartTotal;
}

function openReceiptModal() {
    var paid = parseFloat(document.getElementById('paid-amount').value) || 0;
    var change = Math.max(0, paid - cartTotal);
    var now = new Date();

    document.getElementById('r-invoice').textContent = 'INV-' + now.toISOString().slice(11,19).replace(/:/g,'');
    document.getElementById('r-date').textContent = now.toLocaleString('id-ID');
    document.getElementById('r-total').textContent = fmt(cartTotal);
    document.getElementById('r-paid').textContent = fmt(paid);
    document.getElementById('r-change').textContent = fmt(change);

    var tbody = document.getElementById('r-items');
    tbody.innerHTML = '';
    cart.forEach(function(item) {
        var tr = document.createElement('tr');
        tr.innerHTML = '<td class="ps-0">' + item.name + '<br><small class="text-muted">' + item.qty + ' x ' + fmt(item.price) + '</small></td>'
            + '<td class="text-end pe-0">' + fmt(item.price * item.qty) + '</td>';
        tbody.appendChild(tr);
    });

    var modal = document.getElementById('receipt-modal');
    modal.style.display = 'flex';
}

function printReceipt() {
    var content = document.getElementById('receipt-content').innerHTML;
    var win = window.open('', '_blank', 'width=320,height=600');
    if (!win) { alert('Popup diblokir! Izinkan popup untuk mencetak.'); return; }
    win.document.write('<!DOCTYPE html><html><head><title>Struk</title><style>'
        + '@page{size:80mm auto;margin:0}'
        + 'body{font-family:"Courier New",monospace;font-size:12px;width:72mm;margin:0;padding:3mm;color:#000;line-height:1.3}'
        + '.text-center{text-align:center}.text-end{text-align:right}.fw-bold{font-weight:bold}'
        + '.small,small{font-size:10px}.text-muted{color:#666}'
        + 'table{width:100%;border-collapse:collapse}th,td{padding:2px 0;vertical-align:top}'
        + 'hr{border:none;border-top:1px dashed #000;margin:4px 0}'
        + '.d-flex{display:flex}.justify-content-between{justify-content:space-between}'
        + '.ps-0{padding-left:0}.pe-0{padding-right:0}.mb-3{margin-bottom:8px}.mt-3{margin-top:8px}'
        + '</style></head><body>' + content + '</body></html>');
    win.document.close();
    setTimeout(function() { win.focus(); win.print(); win.close(); }, 500);
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
