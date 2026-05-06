@extends('layouts.app')

@section('breadcrumb')
<li class="breadcrumb-item active text-secondary">Point of Sale</li>
@endsection

@section('full_content')
<div id="pos-area" class="d-flex" style="height: calc(100vh - 64px); background-color: #f9fafb;">
    <!-- Products Section -->
    <div id="pos-products" class="flex-grow-1 p-4 overflow-y-auto">
        <!-- Minimalist Header -->
        <div class="row g-3 mb-4 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-lg search-container shadow-sm border rounded-4 overflow-hidden bg-white">
                    <span class="input-group-text border-0 bg-white ps-3"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="product-search" class="form-control border-0 fs-6 ps-2" placeholder="Cari menu atau produk...">
                </div>
            </div>
            <div class="col-md-7">
                <div class="category-nav d-flex gap-2 overflow-x-auto pb-1">
                    <button class="btn category-item active">Semua Menu</button>
                    @foreach($categories as $category)
                        <button class="btn category-item">{{ $category->name }}</button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Clean Product Grid -->
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-4">
            @forelse($products as $product)
                <div class="col">
                    <div class="item-card add-to-cart" 
                         data-id="{{ $product->id }}" 
                         data-name="{{ $product->name }}" 
                         data-price="{{ $product->defaultPrice?->sell_price ?? ($product->prices->first()?->sell_price ?? 0) }}">
                        <div class="item-badge">{{ $product->category->name ?? 'Umum' }}</div>
                        <div class="item-visual">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div class="item-body">
                            <h6 class="item-name">{{ $product->name }}</h6>
                            <div class="item-price">Rp {{ number_format($product->defaultPrice?->sell_price ?? ($product->prices->first()?->sell_price ?? 0), 0) }}</div>
                        </div>
                        <div class="item-hover-btn">
                            <i class="bi bi-plus-lg"></i>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 opacity-50">
                    <i class="bi bi-inbox display-1"></i>
                    <p class="mt-3 fs-5">Produk tidak tersedia</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Minimalist Cart Sidebar -->
    <div id="pos-cart" class="bg-white border-start d-flex flex-column" style="width: 400px;">
        <div class="p-4 border-bottom">
            <h5 class="fw-bold mb-1">Daftar Pesanan</h5>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary-subtle text-primary rounded-pill px-3" id="cart-count">0 Items</span>
                <span class="text-muted small">#{{ date('dmYHi') }}</span>
            </div>
        </div>

        <div id="pos-cart-items" class="flex-grow-1 p-4 overflow-y-auto">
            <div class="empty-state text-center py-5">
                    <i class="bi bi-basket3 text-primary opacity-50 mb-3" style="font-size: 5rem;"></i>
                    <p class="text-muted fw-semibold">Keranjang masih kosong</p>
                    <p class="text-muted small">Pilih produk untuk mulai transaksi</p>
                </div>
        </div>

        <div class="p-4 bg-light bg-opacity-50">
            <div class="summary-row d-flex justify-content-between mb-2">
                <span class="text-muted">Subtotal</span>
                <span class="fw-semibold text-dark">Rp 0</span>
            </div>
            <div class="summary-row d-flex justify-content-between mb-4">
                <span class="text-dark fw-bold fs-5">Total Bayar</span>
                <span class="text-primary fw-bold fs-5 text-end">Rp 0</span>
            </div>
            <button id="checkout-btn" class="btn btn-primary btn-md w-100 py-2 rounded-4 fw-bold shadow-sm">
                Proses Pembayaran <i class="bi bi-chevron-right ms-2"></i>
            </button>
        </div>
    </div>
</div>

<!-- Simple Modern Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-5 overflow-hidden">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold">Konfirmasi Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <label class="form-label text-muted small fw-bold text-uppercase">Nama Pelanggan</label>
                    <input type="text" id="customer-name" class="form-control form-control-lg border-0 bg-light rounded-4" placeholder="Cth: Budi">
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-7">
                        <label class="form-label text-muted small fw-bold text-uppercase">Metode Pembayaran</label>
                        <select id="payment-method" class="form-select form-select-lg border-0 bg-light rounded-4">
                            <option value="cash">Tunai (Cash)</option>
                            <option value="transfer">Transfer Bank</option>
                        </select>
                    </div>
                    <div class="col-5">
                        <label class="form-label text-muted small fw-bold text-uppercase text-end d-block">Tagihan</label>
                        <h3 id="modal-total" class="fw-bold text-primary text-end">Rp 0</h3>
                    </div>
                </div>

                <div class="mb-4 text-center">
                    <label class="form-label text-muted small fw-bold text-uppercase d-block mb-3">Nominal Diterima</label>
                    <div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 p-3 rounded-5 mb-2">
                        <span class="fs-4 fw-bold text-primary me-2">Rp</span>
                        <input type="number" id="paid-amount" class="form-control border-0 bg-transparent fs-2 fw-bold text-primary p-0 w-75 text-center" placeholder="0">
                    </div>
                    <div class="small text-muted">Klik untuk menginput jumlah pembayaran</div>
                </div>

                <div class="bg-dark text-white p-3 rounded-4 d-flex justify-content-between align-items-center mb-4 shadow-sm">
                    <span class="small opacity-75">KEMBALIAN</span>
                    <h4 id="change-amount" class="fw-bold mb-0">Rp 0</h4>
                </div>

                <button id="confirm-payment" class="btn btn-primary btn-lg w-100 py-3 rounded-4 fw-bold shadow">
                    Selesaikan Transaksi
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-5 overflow-hidden">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 80px; height: 80px;">
                        <i class="bi bi-check-lg fs-1"></i>
                    </div>
                </div>
                <h4 class="fw-bold">Berhasil!</h4>
                <p id="receipt-summary" class="text-muted small mb-4"></p>
                <div class="d-grid gap-2">
                    <a id="print-receipt-link" href="#" target="_blank" class="btn btn-primary rounded-4 py-2 fw-bold">Cetak Struk</a>
                    <button class="btn btn-light rounded-4 py-2 border text-muted" onclick="window.location.reload()">Selesai</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Clean System Typography */
    body { font-family: 'Inter', system-ui, -apple-system, sans-serif; letter-spacing: -0.01em; }
    
    /* Category Navigation */
    .category-item { border: none; background: white; color: #64748b; padding: 10px 24px; border-radius: 14px; font-weight: 600; white-space: nowrap; transition: 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
    .category-item:hover { background: #f1f5f9; color: #1e293b; }
    .category-item.active { background: #2563eb; color: white; box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3); }

    /* Modern Item Card */
    .item-card { background: white; border-radius: 20px; padding: 16px; position: relative; transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1); border: 1px solid #f1f5f9; cursor: pointer; height: 100%; display: flex; flex-direction: column; overflow: hidden; }
    .item-card:hover { transform: translateY(-8px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important; border-color: #dbeafe; }
    .item-badge { position: absolute; top: 12px; left: 12px; background: #f8fafc; color: #64748b; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; padding: 4px 10px; border-radius: 8px; z-index: 1; border: 1px solid #e2e8f0; }
    .item-visual { height: 110px; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: #cbd5e1; background: #f8fafc; border-radius: 14px; margin-bottom: 12px; }
    .item-name { font-size: 0.95rem; font-weight: 700; color: #1e293b; line-height: 1.4; margin-bottom: 8px; height: 2.6rem; overflow: hidden; }
    .item-price { font-size: 1.1rem; font-weight: 800; color: #2563eb; }
    .item-hover-btn { position: absolute; bottom: 16px; right: 16px; background: #2563eb; color: white; width: 32px; height: 32px; border-radius: 10px; display: flex; align-items: center; justify-content: center; opacity: 0; transition: 0.2s; transform: scale(0.8); }
    .item-card:hover .item-hover-btn { opacity: 1; transform: scale(1); }

    /* Cart Sidebar Items */
    #pos-cart { width: 400px; border-left: 1px solid #e0e0e0; height: 100vh; overflow-y: hidden; }
    #pos-cart-items { flex-grow: 1; overflow-y: auto; max-height: calc(100vh - 250px); }
    
    .cart-item-row { background: #f8fafc; border-radius: 16px; padding: 14px; border: 1px solid #f1f5f9; transition: 0.2s; }
    .cart-item-row:hover { background: white; border-color: #2563eb; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
    
    /* Scrollbar */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }

    /* Form Controls */
    .form-control:focus, .form-select:focus { outline: none; box-shadow: none; border-color: #2563eb; }
</style>
@endpush

@push('scripts')
<script>
    // Search functionality
    document.getElementById('product-search').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        filterProducts(query, document.querySelector('.category-item.active').innerText);
    });

    // Category filter functionality
    document.querySelectorAll('.category-item').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('.category-item').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            const query = document.getElementById('product-search').value.toLowerCase();
            filterProducts(query, this.innerText);
        });
    });

    function filterProducts(query, category) {
        document.querySelectorAll('.col').forEach(col => {
            const card = col.querySelector('.item-card');
            if (!card) return;
            
            const name = card.dataset.name.toLowerCase();
            const prodCategory = card.querySelector('.item-badge').innerText.trim();
            
            const matchesQuery = name.includes(query);
            const matchesCategory = (category === 'Semua Menu' || prodCategory === category);
            
            col.style.display = (matchesQuery && matchesCategory) ? '' : 'none';
        });
    }

    // Global click handler for efficiency
    document.addEventListener('click', function(e) {
        const addToCartBtn = e.target.closest('.add-to-cart');
        if (addToCartBtn) {
            const product = {
                id: addToCartBtn.dataset.id,
                name: addToCartBtn.dataset.name,
                price: parseFloat(addToCartBtn.dataset.price),
                qty: 1
            };

            const existing = cart.find(item => item.id === product.id);
            if (existing) {
                existing.qty++;
            } else {
                cart.push(product);
            }
            updateCartUI();
        }
    });

    function updateCartUI() {
        const cartItems = document.getElementById('pos-cart-items');
        const cartCount = document.getElementById('cart-count');
        cartItems.innerHTML = '';
        let subtotal = 0;

        if (cart.length === 0) {
            cartItems.innerHTML = `
                <div class="empty-state text-center py-5">
                    <i class="bi bi-basket3 text-primary opacity-50 mb-3" style="font-size: 5rem;"></i>
                    <p class="text-muted fw-semibold">Keranjang masih kosong</p>
                    <p class="text-muted small">Pilih produk untuk mulai transaksi</p>
                </div>
            `;
        } else {
            cart.forEach(item => {
                subtotal += item.price * item.qty;
                cartItems.innerHTML += `
                    <div class="cart-item-row d-flex justify-content-between align-items-center mb-3">
                        <div class="flex-grow-1 pe-2">
                            <div class="fw-bold text-dark small mb-1">${item.name}</div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-white border text-primary rounded-pill">x${item.qty}</span>
                                <span class="text-muted extra-small">@ ${item.price.toLocaleString()}</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-dark small mb-2">Rp ${(item.price * item.qty).toLocaleString()}</div>
                            <button class="btn btn-sm btn-link text-danger p-0" onclick="removeItem('${item.id}')">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
        }

        cartCount.innerText = `${cart.length} Items`;
        
        const summaryPanel = document.querySelector('.summary-row').parentElement;
        if (summaryPanel) {
            summaryPanel.querySelectorAll('.fw-semibold')[0].innerText = `Rp ${subtotal.toLocaleString()}`;
            summaryPanel.querySelectorAll('.text-primary.fw-bold')[0].innerText = `Rp ${subtotal.toLocaleString()}`;
        }
    }

    function removeItem(id) {
        cart = cart.filter(item => item.id !== id);
        updateCartUI();
    }

    document.getElementById('checkout-btn').addEventListener('click', function() {
        if (cart.length === 0) {
            alert('Pilih produk terlebih dahulu');
            return;
        }
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        document.getElementById('modal-total').innerText = `Rp ${subtotal.toLocaleString()}`;
        document.getElementById('paid-amount').value = ''; 
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
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        const paidAmount = parseFloat(document.getElementById('paid-amount').value);

        if (!paidAmount || paidAmount < subtotal) {
            alert('Nominal pembayaran kurang');
            return;
        }
        
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sesaat...';

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
                customer_name: document.getElementById('customer-name').value,
                payment_method: document.getElementById('payment-method').value,
                paid_amount: paidAmount
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
                document.getElementById('receipt-summary').innerText = `Total: Rp ${subtotal.toLocaleString()}`;
                document.getElementById('print-receipt-link').href = `/pos/${data.data.id}/receipt`;
                new bootstrap.Modal(document.getElementById('successModal')).show();
            } else {
                alert(data.message);
                btn.disabled = false;
                btn.innerHTML = 'Selesaikan Transaksi';
            }
        })
        .catch(() => {
            alert('Error saat mengirim data');
            btn.disabled = false;
            btn.innerHTML = 'Selesaikan Transaksi';
        });
    });
</script>
@endpush
@endsection
