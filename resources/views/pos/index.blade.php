@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item active text-secondary">Point of Sale</li>
@endsection

@section('full_content')
<div id="pos-area" class="d-flex" style="height: calc(100vh - 64px);">
    <!-- Products Section -->
    <div id="pos-products" class="flex-grow-1 p-4 bg-light overflow-y-auto">
        <!-- Search & Categories -->
        <div class="d-flex gap-3 align-items-center mb-4">
            <div class="input-group shadow-sm" style="max-width: 400px;">
                <span class="input-group-text bg-white border-0"><i class="bi bi-search text-primary"></i></span>
                <input type="text" id="product-search" class="form-control border-0" placeholder="Cari produk...">
            </div>
            <div class="d-flex gap-2 overflow-x-auto pb-2 flex-grow-1">
                <button class="btn category-pill active">Semua</button>
                @foreach($categories as $category)
                    <button class="btn category-pill">{{ $category->name }}</button>
                @endforeach
            </div>
        </div>

        <!-- Product Grid -->
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
            @forelse($products as $product)
                <div class="col">
                    <div class="product-card">
                        <div class="text-center mb-2">
                             <div class="rounded bg-light d-flex align-items-center justify-content-center" style="height: 100px;">
                                <i class="bi bi-box-seam fs-1 text-secondary opacity-50"></i>
                             </div>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 text-truncate">{{ $product->name }}</h6>
                            <p class="text-muted small mb-2">{{ $product->category->name ?? '-' }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="price">Rp {{ number_format($product->defaultPrice?->sell_price ?? ($product->prices->first()?->sell_price ?? 0), 0) }}</span>
                                <button class="btn btn-sm btn-primary rounded-pill px-3 add-to-cart" 
                                        data-id="{{ $product->id }}" 
                                        data-name="{{ $product->name }}" 
                                        data-price="{{ $product->defaultPrice?->sell_price ?? ($product->prices->first()?->sell_price ?? 0) }}">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <p class="text-muted">Tidak ada produk ditemukan.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Cart Section -->
    <div id="pos-cart" class="d-flex flex-column bg-white shadow-lg" style="width: 380px; z-index: 10;">
        <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-cart3 me-2"></i>Keranjang</h6>
            <span class="badge bg-primary rounded-pill" id="cart-count">0</span>
        </div>

        <div id="pos-cart-items" class="flex-grow-1 p-3">
            <!-- Cart Items -->
            <div class="text-center text-muted py-5 mt-5">
                <i class="bi bi-cart-x fs-1 opacity-25"></i>
                <p>Keranjang kosong</p>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="p-4 border-top bg-light">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Subtotal</span>
                <span class="fw-bold text-dark">Rp 0</span>
            </div>
            <div class="d-flex justify-content-between mb-4">
                <span class="fw-bold text-primary fs-5">Total</span>
                <span class="fw-bold text-primary fs-5">Rp 0</span>
            </div>
            <div class="d-grid gap-2">
                <button id="checkout-btn" class="btn btn-primary btn-lg fw-bold rounded-pill">Proses Pembayaran</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pembayaran -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body p-4">
                <h5 class="fw-bold mb-4">Ringkasan Pembayaran</h5>
                
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">NAMA PELANGGAN</label>
                    <input type="text" id="customer-name" class="form-control" placeholder="Opsional">
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-muted">TOTAL TAGIHAN</label>
                        <h4 id="modal-total" class="fw-bold text-primary">Rp 0</h4>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-muted">METODE</label>
                        <select id="payment-method" class="form-select">
                            <option value="cash">Tunai</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted">NOMINAL DIBAYAR</label>
                    <input type="number" id="paid-amount" class="form-control form-control-lg" placeholder="Masukkan jumlah uang...">
                </div>

                <div class="bg-light p-3 rounded-3 mb-4">
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold text-muted">KEMBALIAN</span>
                        <h4 id="change-amount" class="fw-bold text-success mb-0">Rp 0</h4>
                    </div>
                </div>

                <div class="d-grid">
                    <button id="confirm-payment" class="btn btn-primary btn-lg rounded-pill shadow">Selesaikan Transaksi</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sukses -->
<div class="modal fade" id="successModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body text-center p-5">
                <div class="display-1 text-success mb-4"><i class="bi bi-check-circle-fill"></i></div>
                <h3 class="fw-bold">Transaksi Berhasil!</h3>
                <p id="receipt-summary" class="text-muted"></p>
                <div class="d-grid gap-2 mt-4">
                    <a id="print-receipt-link" href="#" target="_blank" class="btn btn-primary btn-lg rounded-pill">Cetak Struk</a>
                    <button class="btn btn-light btn-lg rounded-pill" onclick="window.location.reload()">Transaksi Baru</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .category-pill {
        border-radius: 2rem;
        padding: 0.5rem 1.25rem;
        font-weight: 600;
        border: 1px solid #dee2e6;
        background: #fff;
    }
    .category-pill.active {
        background: var(--primary-color);
        color: #fff;
        border-color: var(--primary-color);
    }
    .product-card {
        border: 1px solid #e9ecef;
        border-radius: 1rem;
        padding: 1rem;
        transition: all 0.2s;
    }
    .product-card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
        border-color: var(--primary-color);
    }
    .price { color: var(--primary-color); font-weight: 700; }
</style>
@endpush
@push('scripts')
<script>
    let cart = [];

    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const product = {
                id: this.dataset.id,
                name: this.dataset.name,
                price: parseFloat(this.dataset.price),
                qty: 1
            };

            const existing = cart.find(item => item.id === product.id);
            if (existing) {
                existing.qty++;
            } else {
                cart.push(product);
            }
            updateCartUI();
        });
    });

    function updateCartUI() {
        const cartItems = document.getElementById('pos-cart-items');
        const cartCount = document.getElementById('cart-count');
        cartItems.innerHTML = '';
        let subtotal = 0;

        cart.forEach(item => {
            subtotal += item.price * item.qty;
            cartItems.innerHTML += `
                <div class="cart-item d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                    <div>
                        <div class="fw-bold small">${item.name}</div>
                        <div class="text-primary small">Rp ${item.price.toLocaleString()} x ${item.qty}</div>
                    </div>
                    <button class="btn btn-sm text-danger" onclick="removeItem('${item.id}')"><i class="bi bi-trash"></i></button>
                </div>
            `;
        });

        cartCount.innerText = cart.length;
        document.querySelectorAll('.p-4 .text-dark')[0].innerText = `Rp ${subtotal.toLocaleString()}`;
        document.querySelectorAll('.p-4 .text-primary.fs-5')[1].innerText = `Rp ${subtotal.toLocaleString()}`;
    }

    function removeItem(id) {
        cart = cart.filter(item => item.id !== id);
        updateCartUI();
    }
    document.getElementById('checkout-btn').addEventListener('click', function() {
        if (cart.length === 0) {
            alert('Keranjang kosong!');
            return;
        }
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        document.getElementById('modal-total').innerText = `Rp ${subtotal.toLocaleString()}`;
        document.getElementById('paid-amount').value = ''; // Kosongkan input
        document.getElementById('change-amount').innerText = `Rp 0`;
        new bootstrap.Modal(document.getElementById('paymentModal')).show();
    });

    document.getElementById('paid-amount').addEventListener('input', function() {
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        const paid = parseFloat(this.value) || 0;
        const change = paid - subtotal;
        document.getElementById('change-amount').innerText = `Rp ${change >= 0 ? change.toLocaleString() : '0'}`;
    });

    document.getElementById('confirm-payment').addEventListener('click', function() {
        const customerName = document.getElementById('customer-name').value;
        const paymentMethod = document.getElementById('payment-method').value;
        const paidAmount = parseFloat(document.getElementById('paid-amount').value);
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        
        fetch('{{ route('pos.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                items: cart.map(item => ({
                    product_id: item.id,
                    qty: item.qty,
                    price: item.price,
                    name: item.name
                })),
                customer_name: customerName,
                payment_method: paymentMethod,
                paid_amount: paidAmount
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
                document.getElementById('receipt-summary').innerText = `Total Transaksi: Rp ${paidAmount.toLocaleString()}`;
                document.getElementById('print-receipt-link').href = `/pos/${data.data.id}/receipt`;
                new bootstrap.Modal(document.getElementById('successModal')).show();
            } else {
                alert(data.message || 'Terjadi kesalahan');
            }
        });
    });
</script>
@endpush
@endsection
