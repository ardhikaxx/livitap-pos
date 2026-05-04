# LIVITAP POS — Rules & Specification
> **Laravel 12 · Multi-Tenant · Multi-Role · Universal Point of Sale System**
> Versi: 1.0.0 | Terakhir diperbarui: 2025

---

## DAFTAR ISI

1. [Gambaran Umum Sistem](#1-gambaran-umum-sistem)
2. [Tech Stack](#2-tech-stack)
3. [Struktur Role & Hak Akses](#3-struktur-role--hak-akses)
4. [Modul & Fitur Lengkap](#4-modul--fitur-lengkap)
5. [Struktur Database](#5-struktur-database)
6. [Struktur Direktori Laravel](#6-struktur-direktori-laravel)
7. [Blade Template System](#7-blade-template-system)
8. [Konvensi Kode](#8-konvensi-kode)
9. [API Endpoint (Internal)](#9-api-endpoint-internal)
10. [Business Logic Rules](#10-business-logic-rules)
11. [UI/UX Guidelines](#11-uiux-guidelines)
12. [Konfigurasi & Environment](#12-konfigurasi--environment)
13. [Keamanan (Security Rules)](#13-keamanan-security-rules)

---

## 1. GAMBARAN UMUM SISTEM

**LIVITAP POS** adalah sistem kasir (Point of Sale) berbasis web yang dibangun di atas Laravel 12, dirancang untuk digunakan oleh berbagai jenis usaha:

| Jenis Usaha | Contoh |
|---|---|
| Toko Retail | Toko bunga, toko sembako, toko kelontong, minimarket |
| F&B | Rumah makan, cafe, warung kopi, bakery |
| Jasa | Laundry, barbershop, salon, bengkel |
| Lainnya | Apotek kecil, toko elektronik, toko pakaian |

### Prinsip Utama

- **Fleksibel** — dapat dikonfigurasi sesuai jenis usaha tanpa mengubah kode
- **Multi-Outlet** — satu akun bisnis bisa memiliki banyak cabang/outlet
- **Offline-Ready** — transaksi tetap bisa berjalan saat koneksi internet terganggu (PWA/Service Worker)
- **Responsive** — bisa diakses dari PC, tablet, maupun HP
- **Audit Trail** — semua aktivitas dicatat untuk keamanan dan akuntabilitas

---

## 2. TECH STACK

```
Backend   : Laravel 12 (PHP 8.3+)
Frontend  : Blade + Alpine.js + Tailwind CSS v4
Database  : MySQL 8.0+ / PostgreSQL 15+
Cache     : Redis (wajib untuk queue & session di production)
Queue     : Laravel Queue (Redis driver)
Auth      : Laravel Sanctum (SPA) + Session-based web
Search    : Laravel Scout + Meilisearch (opsional)
Print     : Browser Print API / Thermal Printer via escpos-php
Storage   : Laravel Storage (local / S3-compatible)
PDF       : barryvdh/laravel-dompdf
Excel     : maatwebsite/excel
QR/Barcode: milon/barcode atau SimpleSoftwareIO/simple-qrcode
WebSocket : Laravel Reverb (untuk notifikasi real-time)
```

---

## 3. STRUKTUR ROLE & HAK AKSES

Sistem menggunakan **Spatie Laravel Permission** dengan hierarki role sebagai berikut:

```
Super Admin
└── Owner (per bisnis)
    └── Manager (per outlet)
        ├── Kasir (per outlet)
        ├── Gudang / Stok Keeper (per outlet)
        └── Waiter / Pelayan (khusus F&B)
```

### 3.1 Super Admin

> Akses penuh ke seluruh sistem (level platform).

| Akses | Detail |
|---|---|
| Manajemen Bisnis | CRUD semua bisnis/tenant |
| Manajemen User Global | Blokir/aktifkan user |
| Pengaturan Platform | Konfigurasi global, fitur flag |
| Laporan Global | Melihat semua laporan lintas bisnis |
| Audit Log | Seluruh log aktivitas sistem |

### 3.2 Owner

> Pemilik bisnis. Akses penuh untuk bisnis miliknya.

| Modul | Akses |
|---|---|
| Dashboard Bisnis | Semua outlet, semua laporan |
| Manajemen Outlet | CRUD outlet/cabang |
| Manajemen User | CRUD user dalam bisnisnya |
| Produk & Kategori | CRUD lengkap |
| Supplier | CRUD lengkap |
| Pembelian (Purchase Order) | CRUD + approval |
| Stok & Gudang | Full access |
| Diskon & Promo | CRUD lengkap |
| Laporan | Semua jenis laporan |
| Keuangan | Kas, laba rugi, HPP |
| Pengaturan Bisnis | Konfigurasi toko, pajak, receipt |
| Integrasi | Payment gateway, printer |

### 3.3 Manager

> Manajer outlet. Akses penuh untuk 1 outlet yang ditugaskan.

| Modul | Akses |
|---|---|
| Dashboard Outlet | Hanya outlet sendiri |
| Kasir / POS | Full transaksi |
| Manajemen Produk | Edit (tidak delete) |
| Stok | Penyesuaian stok, transfer stok |
| Pembelian | Buat PO (perlu approval Owner) |
| Laporan | Laporan outlet sendiri |
| User Management | View-only + assign shift |
| Diskon | Bisa buat diskon, butuh approval Owner untuk nominal besar |
| Retur | Approve retur |

### 3.4 Kasir

> Operator kasir harian.

| Modul | Akses |
|---|---|
| Dashboard | Ringkasan shift aktif |
| POS / Transaksi | Buat transaksi, tahan pesanan (hold), batal transaksi (dengan konfirmasi Manager) |
| Pelanggan | Tambah pelanggan baru, cari pelanggan |
| Retur Penjualan | Ajukan retur (perlu approval Manager/Owner) |
| Laporan Shift | Hanya laporan shift sendiri |
| Stok | Lihat stok saja |
| Diskon | Hanya bisa pakai diskon yang sudah ada |
| Pembayaran | Semua metode pembayaran yang aktif |

### 3.5 Gudang (Stock Keeper)

> Bertanggung jawab atas stok fisik.

| Modul | Akses |
|---|---|
| Stok | Full (penyesuaian, opname, transfer) |
| Produk | Lihat + edit stok minimum |
| Pembelian | Terima barang (konfirmasi penerimaan PO) |
| Laporan Stok | Stok masuk, keluar, opname |
| Supplier | Lihat saja |

### 3.6 Waiter / Pelayan (Mode F&B)

> Khusus untuk usaha F&B.

| Modul | Akses |
|---|---|
| Table Management | Lihat & pilih meja |
| Order | Buat pesanan dari meja |
| Menu | Lihat menu + ketersediaan |
| Kitchen Display | Lihat status order dapur |
| Bill | Cetak tagihan ke kasir |

---

## 4. MODUL & FITUR LENGKAP

### 4.1 Modul POS (Point of Sale)

```
POS Interface
├── Pencarian produk (barcode scanner / nama / kode SKU)
├── Kategori produk dengan ikon (shortcut akses cepat)
├── Keranjang transaksi
│   ├── Tambah / kurangi / hapus item
│   ├── Edit harga manual (jika diizinkan role)
│   ├── Diskon per item (nominal / persentase)
│   ├── Diskon per transaksi
│   ├── Catatan per item
│   └── Pilih varian produk
├── Pelanggan
│   ├── Pilih pelanggan existing
│   ├── Tambah pelanggan cepat
│   └── Tampilkan poin loyalty pelanggan
├── Pembayaran
│   ├── Tunai (hitung kembalian otomatis)
│   ├── QRIS / Transfer (konfirmasi manual atau otomatis via API)
│   ├── Kartu Debit/Kredit (EDC manual)
│   ├── E-Wallet (OVO, GoPay, Dana, dll — konfigurabel)
│   ├── Voucher / Gift Card
│   ├── Bayar split (campuran beberapa metode)
│   └── Cicilan (konfigurabel)
├── Hold / Tahan Pesanan (bisa buka kembali)
├── Transaksi offline (sync otomatis saat online)
└── Cetak struk (thermal 58mm / 80mm / A4)
```

### 4.2 Manajemen Produk

```
Produk
├── Informasi Dasar
│   ├── Nama, deskripsi, foto (multiple)
│   ├── SKU (auto-generate / manual)
│   ├── Barcode (generate & cetak)
│   ├── Kategori (multi-level, max 3 level)
│   ├── Brand / Merek
│   ├── Unit satuan (pcs, kg, gram, liter, porsi, dll)
│   └── Tags
├── Harga
│   ├── Harga beli (HPP)
│   ├── Harga jual (bisa beda per outlet)
│   ├── Harga member / tier harga
│   ├── Harga grosir (berdasarkan qty)
│   └── Pajak (PPN, tidak kena pajak, dll)
├── Stok
│   ├── Stok per outlet
│   ├── Stok minimum (trigger alert)
│   ├── Stok maksimum
│   └── Track stok (on/off — untuk produk jasa/tidak berst)
├── Varian
│   ├── Multi-atribut (ukuran, warna, rasa, dll)
│   └── Stok & harga per varian
├── Komposit / Bundle
│   ├── Produk bundle (paket)
│   └── Produk dengan bahan baku (recipe) untuk F&B
├── Status
│   ├── Aktif / Nonaktif
│   ├── Tampil di POS / sembunyikan
│   └── Produk favorit
└── Import/Export via Excel
```

### 4.3 Manajemen Stok & Gudang

```
Stok
├── Stok Opname
│   ├── Buat sesi opname
│   ├── Input stok fisik
│   ├── Generate selisih otomatis
│   └── Approve & update stok
├── Penyesuaian Stok (Adjustment)
│   ├── Tambah stok (kerusakan, sampel, dll)
│   └── Kurangi stok dengan alasan
├── Transfer Stok Antar Outlet
│   ├── Buat permintaan transfer
│   ├── Konfirmasi pengiriman
│   └── Konfirmasi penerimaan
├── Riwayat Pergerakan Stok
│   ├── Setiap perubahan tercatat (siapa, kapan, alasan)
│   └── Filter per produk / tanggal / jenis transaksi
└── Alert Stok Minimum
    ├── Notifikasi in-app
    └── Email/WhatsApp ke Owner/Manager
```

### 4.4 Pembelian (Purchase Management)

```
Pembelian
├── Supplier
│   ├── Data lengkap supplier
│   ├── Produk per supplier
│   └── Riwayat pembelian per supplier
├── Purchase Order (PO)
│   ├── Buat PO manual / dari alert stok minimum
│   ├── Approval flow (Kasir → Manager → Owner)
│   ├── Kirim PO ke supplier (email / cetak)
│   └── Status PO (draft, dikirim, sebagian diterima, selesai)
├── Penerimaan Barang (GRN)
│   ├── Konfirmasi penerimaan berdasarkan PO
│   ├── Input qty aktual diterima
│   ├── Update stok otomatis
│   └── Catat selisih
└── Retur Pembelian
    ├── Kembalikan barang ke supplier
    └── Adjust stok & hutang
```

### 4.5 Manajemen Pelanggan (CRM)

```
Pelanggan
├── Data Pelanggan
│   ├── Nama, telepon, email, alamat
│   ├── Tanggal lahir (untuk promo ulang tahun)
│   ├── Jenis kelamin
│   └── Foto
├── Loyalty Program (Poin)
│   ├── Konfigurasi nilai poin (Rp X = Y poin)
│   ├── Penukaran poin saat transaksi
│   ├── Expire poin (konfigurabel)
│   └── Riwayat poin
├── Member / Grup Pelanggan
│   ├── Reguler, Silver, Gold, Platinum (konfigurabel)
│   ├── Diskon otomatis per tier
│   └── Harga khusus per tier
├── Riwayat Transaksi Pelanggan
├── Hutang Pelanggan (Piutang)
│   ├── Catat transaksi kredit
│   ├── Bayar cicilan
│   └── Laporan piutang
└── Export data pelanggan (Excel / CSV)
```

### 4.6 Diskon & Promosi

```
Diskon & Promosi
├── Jenis Diskon
│   ├── Diskon nominal (Rp)
│   ├── Diskon persentase (%)
│   ├── Diskon per item tertentu
│   ├── Diskon per kategori
│   └── Diskon per total belanja (minimum pembelian)
├── Promosi
│   ├── Beli X Gratis Y (Buy X Get Y)
│   ├── Bundling harga khusus
│   ├── Happy hour (berdasarkan jam)
│   ├── Promo hari tertentu
│   └── Flash sale (waktu terbatas)
├── Voucher / Kupon
│   ├── Generate kode voucher
│   ├── Batas penggunaan (1x / unlimited)
│   ├── Masa berlaku
│   └── Laporan penggunaan voucher
└── Konfiguarsi
    ├── Diskon tidak bisa digabung / bisa digabung
    ├── Prioritas diskon
    └── Approval untuk diskon manual besar
```

### 4.7 Laporan & Analitik

```
Laporan
├── Laporan Penjualan
│   ├── Penjualan harian / mingguan / bulanan / custom range
│   ├── Penjualan per produk
│   ├── Penjualan per kategori
│   ├── Penjualan per kasir
│   ├── Penjualan per metode pembayaran
│   ├── Penjualan per outlet
│   └── Grafik trend penjualan
├── Laporan Stok
│   ├── Kartu stok per produk
│   ├── Stok masuk & keluar
│   ├── Produk fast-moving / slow-moving
│   ├── Nilai stok (HPP)
│   └── Riwayat opname
├── Laporan Keuangan
│   ├── Laporan laba kotor (pendapatan - HPP)
│   ├── Rekap kas harian
│   ├── Laporan per shift kasir
│   ├── Hutang / Piutang
│   └── Laporan pengeluaran
├── Laporan Pembelian
│   ├── Rekap pembelian per supplier
│   ├── Riwayat PO
│   └── Hutang ke supplier
├── Laporan Pelanggan
│   ├── Pelanggan terbaik (by nilai transaksi)
│   ├── Penggunaan poin
│   └── Segmentasi pelanggan
├── Dashboard Visual
│   ├── Omzet hari ini vs kemarin
│   ├── Produk terlaris
│   ├── Grafik penjualan
│   └── Alert stok minimum
└── Export
    ├── PDF (via DomPDF)
    └── Excel (via Maatwebsite)
```

### 4.8 Manajemen Kas & Shift

```
Kas & Shift
├── Buka Shift
│   ├── Input modal awal (uang di laci)
│   └── Catat nama kasir & waktu buka
├── Setoran Kas (Cash In)
│   └── Tambahan uang ke laci selama shift
├── Pengeluaran Kas (Cash Out / Expense)
│   ├── Catat pengeluaran operasional
│   └── Kategori pengeluaran (belanja, transport, dll)
├── Tutup Shift
│   ├── Hitung total tunai
│   ├── Input uang aktual di laci
│   ├── Sistem hitung selisih otomatis
│   └── Cetak laporan shift
└── Rekap Shift
    ├── Total penjualan per metode pembayaran
    ├── Total transaksi
    ├── Total void/batal
    └── Selisih kas
```

### 4.9 Modul F&B (Opsional — diaktifkan via Pengaturan)

```
F&B Features
├── Table Management
│   ├── Layout meja visual (drag & drop setup)
│   ├── Status meja (kosong, terisi, reserved, minta bill)
│   ├── Gabung meja
│   └── Pindah meja
├── Order Management
│   ├── Buat order per meja / takeaway / delivery
│   ├── Kirim order ke dapur
│   ├── Order bertahap (tambah order)
│   ├── Catatan per item (tidak pedas, extra sambal, dll)
│   └── Void item (dengan alasan)
├── Kitchen Display System (KDS)
│   ├── Tampilkan order masuk real-time
│   ├── Tandai item selesai dibuat
│   ├── Alert order lama (belum diproses > X menit)
│   └── Filter per station (grill, minuman, dll)
├── Menu Digital (QR Order)
│   ├── Generate QR per meja
│   ├── Pelanggan scan & pesan sendiri
│   └── Order masuk ke sistem kasir
├── Resep & HPP
│   ├── Daftar bahan baku
│   ├── Resep per menu (komposisi bahan)
│   ├── Hitung HPP otomatis
│   └── Kurangi stok bahan baku otomatis saat transaksi
└── Delivery Order
    ├── Catat pesanan delivery
    ├── Data penerima & alamat
    ├── Status pengiriman
    └── Integrasi GoFood / GrabFood (manual input)
```

### 4.10 Manajemen Pengguna & Outlet

```
User & Outlet Management
├── Outlet / Cabang
│   ├── Nama, alamat, telepon outlet
│   ├── Logo & informasi struk per outlet
│   ├── Konfigurasi pajak per outlet
│   └── Timezone per outlet
├── User
│   ├── CRUD user
│   ├── Assign role & outlet
│   ├── Reset password
│   ├── Foto profil
│   └── Status aktif/nonaktif
├── Jadwal & Shift Karyawan (opsional)
│   ├── Buat jadwal shift
│   ├── Absensi sederhana (check-in/out)
│   └── Laporan kehadiran
└── Log Aktivitas
    ├── Semua aksi user tercatat
    └── Filter per user / aksi / tanggal
```

### 4.11 Pengaturan Sistem

```
Pengaturan
├── Profil Bisnis
│   ├── Nama bisnis, logo, alamat, NPWP
│   └── Jenis usaha (retail / F&B / jasa)
├── Struk / Receipt
│   ├── Header & footer struk
│   ├── Tampilkan/sembunyikan kolom
│   ├── Ukuran kertas (58mm / 80mm / A4)
│   └── Preview & test print
├── Pajak
│   ├── Aktifkan PPN (default 11%)
│   ├── Pajak sudah termasuk harga / belum
│   └── Pajak khusus per kategori produk
├── Metode Pembayaran
│   ├── Aktifkan/nonaktifkan per metode
│   └── Nama & biaya tambahan per metode
├── Notifikasi
│   ├── Email (SMTP konfigurabel)
│   ├── WhatsApp (via API 3rd party)
│   └── In-app notification
├── Integrasi
│   ├── Payment Gateway (Midtrans / Xendit)
│   ├── Printer thermal (ESC/POS)
│   └── Barcode scanner
├── Backup & Restore
│   ├── Backup database manual
│   ├── Backup otomatis terjadwal
│   └── Restore dari file backup
└── Fitur Toggle
    ├── Aktifkan mode F&B
    ├── Aktifkan loyalty poin
    ├── Aktifkan modul delivery
    └── Aktifkan multi-currency (opsional)
```

### 4.12 Retur & Refund

```
Retur
├── Retur Penjualan
│   ├── Pilih transaksi asal
│   ├── Pilih item yang diretur
│   ├── Alasan retur
│   ├── Metode refund (tunai / poin / kredit akun)
│   ├── Update stok otomatis
│   └── Approval flow (Kasir → Manager)
└── Retur Pembelian
    ├── Pilih PO / GRN asal
    ├── Item & qty yang dikembalikan
    └── Update stok & hutang ke supplier
```

---

## 5. STRUKTUR DATABASE

### Konvensi Penamaan

- Tabel: `snake_case` plural (`products`, `sale_items`)
- Foreign key: `{table_singular}_id` (`product_id`, `outlet_id`)
- Timestamps: semua tabel memiliki `created_at`, `updated_at`
- Soft delete: tabel utama menggunakan `deleted_at`
- UUID: gunakan UUID v7 untuk `id` tabel sensitif (transaksi, user)

### Tabel Utama

```sql
-- Bisnis / Tenant
businesses (id, name, slug, type[retail|fnb|service], logo, address, phone, email, npwp, settings:json, is_active, created_at, updated_at, deleted_at)

-- Outlet / Cabang
outlets (id, business_id, name, address, phone, logo, tax_settings:json, receipt_settings:json, is_active, created_at, updated_at, deleted_at)

-- User
users (id:uuid, business_id, name, email, phone, password, photo, is_active, last_login_at, created_at, updated_at, deleted_at)

-- User-Outlet (pivot — user bisa di banyak outlet)
outlet_user (user_id, outlet_id, is_primary)

-- Kategori Produk (nested set / parent_id)
categories (id, business_id, parent_id, name, slug, icon, color, sort_order, is_active)

-- Produk
products (id, business_id, category_id, brand_id, name, slug, sku, barcode, description, unit, track_stock, has_variant, is_composite, photo, is_active, is_pos_visible, is_favorite, created_at, updated_at, deleted_at)

-- Harga Produk (per outlet)
product_prices (id, product_id, outlet_id, buy_price, sell_price, min_price)

-- Varian Produk
product_variants (id, product_id, name, sku, barcode, photo, buy_price, sell_price)
product_variant_options (id, product_id, attribute_name, attribute_value)

-- Stok Produk (per outlet, per varian)
product_stocks (id, product_id, outlet_id, variant_id:nullable, qty, min_qty, max_qty)

-- Pergerakan Stok
stock_movements (id, product_id, outlet_id, variant_id, type[purchase|sale|adjustment|transfer|opname|return], reference_type, reference_id, qty_before, qty_change, qty_after, notes, user_id, created_at)

-- Supplier
suppliers (id, business_id, name, code, contact_person, phone, email, address, bank_account:json, notes, is_active)

-- Purchase Order
purchase_orders (id, business_id, outlet_id, supplier_id, po_number, status[draft|sent|partial|received|cancelled], order_date, expected_date, notes, subtotal, discount, tax, total, approved_by, approved_at, created_by, created_at, updated_at)

purchase_order_items (id, purchase_order_id, product_id, variant_id, qty_ordered, qty_received, unit, buy_price, subtotal, notes)

-- Penerimaan Barang
goods_receipts (id, purchase_order_id, outlet_id, receipt_number, receipt_date, notes, received_by, created_at)
goods_receipt_items (id, goods_receipt_id, purchase_order_item_id, product_id, variant_id, qty_received, notes)

-- Pelanggan
customers (id, business_id, name, phone, email, address, gender, birthdate, photo, tier[regular|silver|gold|platinum], points, credit_limit, notes, is_active, created_at, updated_at, deleted_at)

-- Transaksi Penjualan
sales (id:uuid, outlet_id, user_id, customer_id:nullable, invoice_number, type[sale|refund], status[paid|partial|unpaid|void], sale_date, subtotal, discount_amount, tax_amount, total, paid_amount, change_amount, notes, table_id:nullable, order_type[dine_in|takeaway|delivery], shift_id, created_at, updated_at)

-- Item Transaksi
sale_items (id, sale_id, product_id, variant_id, name_snapshot, sku_snapshot, qty, unit_price, discount_amount, tax_amount, subtotal, buy_price, notes)

-- Pembayaran
sale_payments (id, sale_id, method[cash|qris|transfer|debit|credit|ewallet|voucher|points|credit_account], amount, reference_number, notes, created_at)

-- Shift Kasir
shifts (id, outlet_id, user_id, status[open|closed], opened_at, closed_at, opening_cash, closing_cash, expected_cash, difference, notes)

-- Kas Harian
cash_flows (id, outlet_id, shift_id, user_id, type[in|out], amount, category, description, created_at)

-- Diskon
discounts (id, business_id, name, type[percentage|nominal|bogo|bundle], value, min_purchase, max_discount, applies_to[all|category|product], target_ids:json, start_date, end_date, usage_limit, used_count, is_active)

-- Voucher
vouchers (id, discount_id, code, used_by:nullable, used_at:nullable, expires_at, is_active)

-- Meja (F&B)
tables (id, outlet_id, name, capacity, area, qr_code, status[empty|occupied|reserved|requesting_bill], current_sale_id:nullable, sort_order)

-- Order Dapur (KDS)
kitchen_orders (id, sale_id, table_id, status[pending|processing|ready|served|cancelled], notes, printed_at, created_at, updated_at)
kitchen_order_items (id, kitchen_order_id, sale_item_id, status[pending|processing|ready|served|cancelled], notes)

-- Resep / Bahan Baku
recipes (id, product_id, notes)
recipe_ingredients (id, recipe_id, product_id, qty, unit)

-- Stok Opname
stock_opnames (id, outlet_id, status[open|closed], notes, opened_by, closed_by, opened_at, closed_at)
stock_opname_items (id, stock_opname_id, product_id, variant_id, system_qty, actual_qty, difference, notes)

-- Log Aktivitas
activity_logs (id, user_id, business_id, outlet_id, action, model_type, model_id, old_values:json, new_values:json, ip_address, user_agent, created_at)

-- Poin Loyalitas
point_transactions (id, customer_id, sale_id:nullable, type[earn|redeem|expire|adjustment], points, balance_before, balance_after, notes, created_at)

-- Notifikasi
notifications (id, user_id, type, title, body, data:json, read_at, created_at)
```

---

## 6. STRUKTUR DIREKTORI LARAVEL

```
app/
├── Console/
│   └── Commands/
│       ├── GenerateDailyReport.php
│       ├── ExpirePoints.php
│       └── SyncOfflineTransactions.php
├── Exceptions/
│   ├── InsufficientStockException.php
│   ├── ShiftNotOpenException.php
│   └── Handler.php
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   ├── API/
│   │   │   ├── POS/
│   │   │   │   ├── SaleController.php
│   │   │   │   ├── CartController.php
│   │   │   │   └── PaymentController.php
│   │   │   ├── Product/
│   │   │   ├── Stock/
│   │   │   ├── Report/
│   │   │   └── ...
│   │   └── Web/
│   │       ├── DashboardController.php
│   │       ├── ProductController.php
│   │       └── ...
│   ├── Middleware/
│   │   ├── CheckBusinessActive.php
│   │   ├── CheckOutletAccess.php
│   │   ├── CheckShiftOpen.php
│   │   └── SetActiveOutlet.php
│   └── Requests/
│       ├── Sale/
│       │   ├── StoreSaleRequest.php
│       │   └── ProcessPaymentRequest.php
│       └── Product/
│           └── StoreProductRequest.php
├── Models/
│   ├── Business.php
│   ├── Outlet.php
│   ├── User.php
│   ├── Product.php
│   ├── ProductStock.php
│   ├── Sale.php
│   ├── SaleItem.php
│   ├── Customer.php
│   ├── Shift.php
│   └── ...
├── Services/
│   ├── SaleService.php          # Logic utama transaksi POS
│   ├── StockService.php         # Manajemen stok
│   ├── PaymentService.php       # Pemrosesan pembayaran
│   ├── ReportService.php        # Generate laporan
│   ├── DiscountService.php      # Kalkulasi diskon
│   ├── PointService.php         # Loyalty poin
│   ├── ShiftService.php         # Manajemen shift
│   ├── ReceiptService.php       # Generate struk
│   └── KitchenService.php       # F&B kitchen order
├── Repositories/
│   ├── ProductRepository.php
│   ├── SaleRepository.php
│   └── StockRepository.php
├── Events/
│   ├── SaleCompleted.php
│   ├── StockLow.php
│   └── ShiftClosed.php
├── Listeners/
│   ├── UpdateCustomerPoints.php
│   ├── DeductProductStock.php
│   ├── SendLowStockAlert.php
│   └── PrintReceiptListener.php
├── Jobs/
│   ├── GenerateReportJob.php
│   ├── SendNotificationJob.php
│   └── SyncOfflineDataJob.php
└── Traits/
    ├── HasAuditLog.php
    ├── HasBusiness.php
    └── BelongsToOutlet.php

database/
├── migrations/
│   ├── 2024_01_01_000001_create_businesses_table.php
│   └── ...
└── seeders/
    ├── DatabaseSeeder.php
    ├── SuperAdminSeeder.php
    ├── DemoDataSeeder.php
    └── PermissionSeeder.php

resources/
├── views/
│   ├── layouts/
│   │   ├── app.blade.php
│   │   ├── pos.blade.php       # Layout khusus POS (minimal UI)
│   │   └── auth.blade.php
│   ├── pos/
│   │   └── index.blade.php
│   ├── dashboard/
│   ├── products/
│   └── ...
└── js/
    ├── pos/
    │   ├── cart.js
    │   ├── payment.js
    │   └── offline-sync.js
    └── app.js

routes/
├── web.php
├── api.php
└── channels.php
```

---

## 7. KONVENSI KODE

### Penamaan

```php
// Controller: ResourceController (singular)
class ProductController extends Controller {}
class SaleController extends Controller {}

// Model: PascalCase singular
class ProductStock extends Model {}
class SaleItem extends Model {}

// Service: PascalCase + Service suffix
class SaleService {}

// Method: camelCase, deskriptif
public function processPayment(Sale $sale, array $payments): void {}
public function calculateDiscount(Cart $cart): float {}

// Event: PascalCase, kejadian yang sudah terjadi
class SaleCompleted {}
class StockLow {}
```

### Model Convention

```php
class Sale extends Model
{
    use SoftDeletes, HasUuids, HasAuditLog, BelongsToOutlet;

    protected $fillable = [...];

    protected $casts = [
        'sale_date' => 'datetime',
        'subtotal'  => 'decimal:2',
        'total'     => 'decimal:2',
    ];

    // Scope wajib ada untuk multi-outlet
    public function scopeForOutlet(Builder $query, int $outletId): Builder
    {
        return $query->where('outlet_id', $outletId);
    }

    // Scope untuk filter periode
    public function scopeInPeriod(Builder $query, Carbon $from, Carbon $to): Builder
    {
        return $query->whereBetween('sale_date', [$from, $to]);
    }
}
```

### Service Pattern

```php
class SaleService
{
    public function __construct(
        private readonly StockService $stockService,
        private readonly DiscountService $discountService,
        private readonly PointService $pointService,
    ) {}

    /**
     * @throws InsufficientStockException
     * @throws ShiftNotOpenException
     */
    public function createSale(array $data, User $cashier): Sale
    {
        return DB::transaction(function () use ($data, $cashier) {
            // 1. Validasi shift aktif
            // 2. Validasi stok
            // 3. Hitung diskon
            // 4. Simpan sale & sale_items
            // 5. Kurangi stok
            // 6. Proses pembayaran
            // 7. Update poin pelanggan
            // 8. Fire event SaleCompleted
        });
    }
}
```

### Response Format API

```json
// Success
{
    "success": true,
    "message": "Transaksi berhasil disimpan",
    "data": { ... },
    "meta": { "invoice_number": "INV-2025-001234" }
}

// Error
{
    "success": false,
    "message": "Stok tidak mencukupi",
    "errors": {
        "product_id_5": "Stok tersisa 2, diminta 5"
    }
}
```

---

## 8. API ENDPOINT (INTERNAL)

Semua endpoint prefixed `/api/v1/` dan menggunakan Sanctum auth.

```
Auth
POST   /auth/login
POST   /auth/logout
GET    /auth/me

POS
POST   /pos/cart/calculate          # Hitung total + diskon tanpa simpan
POST   /pos/sale                    # Buat & selesaikan transaksi
GET    /pos/sale/{id}/receipt       # Data untuk cetak struk
POST   /pos/sale/{id}/void          # Void transaksi
POST   /pos/hold                    # Tahan pesanan
GET    /pos/holds                   # List pesanan ditahan

Produk
GET    /products                    # List produk (filter, search, paginate)
GET    /products/search?q=          # Search cepat untuk POS
GET    /products/{id}
POST   /products
PUT    /products/{id}
DELETE /products/{id}
POST   /products/import             # Import via Excel

Stok
GET    /stocks                      # Stok semua produk
POST   /stocks/adjustment           # Penyesuaian stok
POST   /stocks/transfer             # Transfer antar outlet
POST   /stocks/opname               # Buat sesi opname
POST   /stocks/opname/{id}/close    # Tutup & apply opname

Shift
POST   /shifts/open                 # Buka shift
GET    /shifts/active               # Shift aktif user ini
POST   /shifts/{id}/close           # Tutup shift

Laporan
GET    /reports/sales               # Laporan penjualan
GET    /reports/products            # Laporan produk
GET    /reports/stock               # Laporan stok
GET    /reports/cashier             # Laporan kasir
GET    /reports/shift/{id}          # Laporan per shift
POST   /reports/export              # Export ke PDF/Excel

Pelanggan
GET    /customers
POST   /customers
PUT    /customers/{id}
GET    /customers/{id}/transactions
GET    /customers/{id}/points

Meja (F&B)
GET    /tables
PATCH  /tables/{id}/status
POST   /tables/merge
POST   /tables/{id}/move-to/{targetId}

Kitchen
GET    /kitchen/orders              # (WebSocket juga)
PATCH  /kitchen/orders/{id}/status
```

---

## 9. BUSINESS LOGIC RULES

### Transaksi POS

- Transaksi tidak boleh dibuat jika shift belum dibuka.
- Void transaksi hanya bisa dilakukan pada transaksi di shift yang sama (hari yang sama), kecuali Manager/Owner.
- Jika stok produk tidak cukup dan `track_stock = true`, transaksi ditolak dengan pesan error spesifik per produk.
- Harga di struk menggunakan `name_snapshot`, `sku_snapshot`, dan `unit_price` dari `sale_items` — bukan dari tabel `products` (harga bisa berubah di kemudian hari).
- Transaksi yang sudah `void` tidak bisa dibayar ulang.
- Kembalian uang tunai dihitung: `kembalian = paid_amount - total` (tidak boleh negatif untuk tunai).

### Stok

- Pergerakan stok **selalu** dicatat di `stock_movements` — tidak ada perubahan stok tanpa jejak.
- Transfer stok antar outlet membutuhkan konfirmasi 2 pihak (pengirim + penerima).
- Stok opname yang sudah ditutup tidak bisa diubah.
- Alert stok minimum dikirim hanya 1x per hari per produk per outlet (throttle via cache).

### Diskon

- Diskon tidak bisa menjadikan total transaksi negatif (minimal Rp 0).
- Jika ada diskon otomatis (promo aktif) DAN diskon manual, sistem pilih yang menguntungkan pelanggan (nilai lebih besar), kecuali konfigurasi bisnis mengharuskan persetujuan.
- Voucher sekali pakai langsung dinonaktifkan setelah transaksi selesai, bukan draft/hold.

### Shift & Kas

- Hanya 1 shift aktif per kasir per outlet pada saat bersamaan.
- Penutupan shift memerlukan input jumlah uang aktual di laci; selisih dicatat dan dilaporkan.
- Manager/Owner bisa force-close shift kasir yang tidak hadir.

### Poin Loyalitas

- Poin hanya diberikan untuk transaksi dengan status `paid`.
- Jika transaksi di-void, poin yang diberikan dicabut kembali.
- Expire poin dijalankan via scheduled command harian.

### F&B

- Order tidak bisa diproses jika meja berstatus `empty` dan tidak ada `current_sale_id`.
- Item yang sudah dikirim ke dapur (status `processing`/`ready`) tidak bisa dihapus tanpa alasan dan approval.
- Gabung meja: sale utama milik meja pertama dipilih, semua item meja lain dipindah.

---

## 10. UI/UX GUIDELINES

### Layout Utama

```
Sidebar kiri : Navigasi modul (collapsible di mobile)
Header       : Nama outlet aktif, user, notifikasi, tombol buka POS
Content Area : Konten utama
```

### POS Interface

- Layout **split screen**: kiri = katalog produk, kanan = keranjang & pembayaran.
- Di mobile/tablet: tab antara katalog dan keranjang.
- Tombol produk besar, minimal typing untuk kasir.
- Shortcut keyboard: `F1` buka POS, `F2` focus search, `Enter` konfirmasi pembayaran.
- Warna status meja: hijau = kosong, kuning = terisi, merah = minta bill, abu = reserved.

### Warna & Tema

```
Primary   : #2563EB (biru — kepercayaan, profesional)
Success   : #16A34A (hijau)
Warning   : #D97706 (kuning/amber)
Danger    : #DC2626 (merah)
Neutral   : #374151 (abu-abu gelap)
Background: #F9FAFB
```

### Tabel Data

- Selalu ada pagination (default 25 per halaman).
- Ada filter dan search di atas tabel.
- Kolom sortable dengan indicator arah.
- Action button di kolom terakhir: lihat, edit, hapus.

### Form

- Validasi real-time (Alpine.js).
- Error message di bawah field.
- Loading state di tombol submit.
- Konfirmasi dialog sebelum aksi destructive (delete, void, close shift).

---

### Konfigurasi via `config/livitap.php`

```php
return [
    'pos' => [
        'max_discount_percent'   => 50,    // Maksimal diskon manual tanpa approval
        'void_same_shift_only'   => true,  // Void hanya di shift yang sama
        'allow_negative_stock'   => false, // Izinkan stok negatif
    ],
    'stock' => [
        'low_stock_alert_cooldown_hours' => 24,
    ],
    'receipt' => [
        'paper_sizes' => ['58mm', '80mm', 'A4'],
        'default_size' => '80mm',
    ],
    'loyalty' => [
        'points_per_rupiah' => 1000, // Rp 1.000 = 1 poin
        'point_value'       => 10,   // 1 poin = Rp 10
        'expire_days'       => 365,
    ],
];
```

---

## 12. KEAMANAN (SECURITY RULES)

### Autentikasi & Otorisasi

- Semua route dilindungi `auth` middleware.
- Setiap aksi terhadap data outlet melewati middleware `CheckOutletAccess` untuk memastikan user memiliki akses ke outlet tersebut.
- **Policy** wajib untuk setiap model utama (`ProductPolicy`, `SalePolicy`, dll).
- Permission menggunakan Spatie Laravel Permission — tidak ada hardcode role check `if ($user->role === 'kasir')`, gunakan `$user->can('create-sale')`.

### Data

- Semua input divalidasi via `FormRequest`.
- Gunakan `$fillable` di model, **tidak** menggunakan `$guarded = []`.
- Data keuangan (harga, diskon, total) disimpan dalam integer (sen/satuan terkecil) **atau** decimal(15,2) — konsisten di seluruh sistem. Pilihan: **decimal(15,2)**.
- Tidak ada query raw SQL kecuali sangat diperlukan dan wajib menggunakan binding parameter.

### Audit & Log

- Semua perubahan data sensitif (stok, harga, void transaksi, perubahan role) tercatat di `activity_logs` via trait `HasAuditLog`.
- Log menyimpan: `user_id`, `action`, `old_values`, `new_values`, `ip_address`, `created_at`.

### Lainnya

- Rate limiting pada endpoint login: 5 percobaan per menit per IP.
- CSRF protection aktif untuk semua form web.
- Semua file upload divalidasi tipe MIME dan ukuran maksimal (foto produk: 2MB, max 5 foto).
- Backup database dienkripsi sebelum disimpan.
- Password hashed dengan `bcrypt` (default Laravel, cost factor 12 di production).

---

## CATATAN IMPLEMENTASI

> Saat membangun fitur baru, ikuti urutan ini:
> 1. Migration → 2. Model + Relationship → 3. Policy → 4. FormRequest → 5. Service → 6. Controller → 7. Route → 8. View/Blade → 9. Test

> Fitur yang **wajib** ada di MVP (v1.0):
> Modul 4.1 (POS), 4.2 (Produk), 4.3 (Stok dasar), 4.7 (Laporan penjualan & shift), 4.8 (Kas & Shift), 4.10 (User & Outlet), 4.11 (Pengaturan dasar)

> Fitur yang bisa ditambahkan di v1.1+:
> F&B (4.9), CRM lengkap (4.5), Loyalty (bagian dari 4.5), Delivery, WhatsApp notifikasi, Integrasi payment gateway otomatis

---

*Dokumen ini adalah acuan hidup — perbarui setiap kali ada perubahan arsitektur atau keputusan teknis yang signifikan.*
