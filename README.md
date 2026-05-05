# Livitap POS - Sistem Point of Sale untuk Usaha Retail

**Livitap POS** adalah sistem Point of Sale (POS) lengkap yang dirancang untuk_kedai, kafe, restoran, dan usaha retail_. Sistem ini menyediakan antarmuka yang cepat dan intuitif untuk mengelola transaksi, inventaris, laporan, dan operasi toko harian.

## 🌟 Fitur Utama

### 💰 Point of Sale (POS)
- **Antarmuka Cepat & Responsif** - Transaksi penjualan dalam hitungan detik dengan UI yang dioptimalkan untuk touchscreen
- **Keranjang Belanja Interaktif** - Tambah/hapus item, diskon per-item atau total, service charge
- **Multiple Payment Methods** - Tunai, kartu, QRIS, transfer, atau kombinasi
- **Struk Customizable** - Cetak struk dengan logo dan info toko
- **Tipe Order** - Dine-in, Takeaway, Delivery
- **Hold & Resume** - Tunda transaksi dan lanjutkan nanti

### 📦 Manajemen Inventaris
- **Real-time Stock Tracking** - Pantau stok toko secara real-time
- **Stock Adjustment** - Koreksi stok manual (adjustment, damage, loss)
- **Product Variants** - Variasi warna, ukuran, atau varian lainnya
- **Low Stock Alerts** - Notifikasi saat stok mencapai minimum
- **Import/Export** - Import produk dari Excel
- **Barcode & SKU** - Generate barcode otomatis

### 👥 Manajemen Pelanggan
- **Customer Database** - Simpan data pelanggan dengan histori pembelian
- **Customer Groups** - Klasifikasikan pelanggan (regular, premium)
- **Sales History** - Riwayat pembelian per-pelanggan

### 📊 Laporan & Analytics
- **Sales Dashboard** - Ringkasan penjualan harian, mingguan, bulanan
- **Sales by Category** - Analisis produk terlaris
- **Profit & Loss** - Hitung margin dan laba/rugi
- **Tax Reports** - Laporan pajak sesuai regulasi
- **Z-Report** - Laporan penjualan per-shift
- **Export to Excel** - Unduh laporan dalam format Excel

### 👤 User & Role Management
- **Role-Based Access Control** - Tentukan hak akses berdasarkan peran (admin, kasir, manajer)
- **Activity Logging** - Lacak semua aksi pengguna
- **Shift Management** - Kelola shift kasir dan rekonsiliasi kas

### 💵 Cash & Finance
- **Opening/Closing Cash** - Rekonsiliasi kas harian
- **Cash Flow Tracking** - Pencatatan pemasukan dan pengeluaran
- **Expense Management** - Catat biaya operasional
- **Sales Payment Records** - Lacak pembayaran per-transaksi

## 🛠️ Teknologi

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Blade Templates + Tailwind CSS 4
- **JavaScript**: Vanilla JS + Axios
- **Database**: MySQL
- **Authentication**: Laravel Sanctum (session-based)
- **Authorization**: Spatie Laravel Permission
- **Excel Export**: Maatwebsite Excel
- **Development Tools**: Laravel Pint, PHPUnit

## 📁 Struktur Project

```
livitap/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Controller untuk semua modul
│   │   │   ├── POS/         # POS-specific controllers
│   │   │   ├── ReportController.php
│   │   │   ├── ProductController.php
│   │   │   └── ...
│   │   ├── Middleware/      # Custom middleware
│   │   └── Requests/        # Form request validation
│   ├── Models/              # Eloquent models
│   │   ├── Sale.php
│   │   ├── Product.php
│   │   ├── Shift.php
│   │   └── ...
│   ├── Policies/            # Authorization policies
│   ├── Observers/           # Model observers
│   ├── Services/            # Business logic services
│   │   ├── SaleService.php
│   │   ├── ReportService.php
│   │   ├── StockService.php
│   │   └── ...
│   └── Traits/              # Reusable traits
├── database/
│   ├── migrations/          # Database schema migrations
│   └── seeders/             # Data seeders
├── resources/
│   └── views/
│       ├── pos/             # POS interface (index.blade.php, receipt.blade.php)
│       ├── reports/         # Report views
│       ├── products/        # Product management views
│       └── layouts/         # Master layouts
├── routes/
│   ├── web.php              # Web routes
│   └── api.php              # API routes
├── storage/
├── public/                  # Assets (CSS, JS, images)
└── .env.example             # Environment configuration
```

## 🚀 Instalasi & Setup

### Prerequisites
- PHP 8.2 atau lebih tinggi
- Composer
- Node.js & npm
- MySQL 8.0+ atau MariaDB
- Git

### Langkah Instalasi

1. **Clone repository**
   ```bash
   git clone https://github.com/ardhikaxx/livitap-pos.git
   cd livitap-pos
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Setup database**
   - Buat database MySQL: `livitap_pos`
   - Edit `.env` dengan kredensial database

5. **Jalankan migrations**
   ```bash
   php artisan migrate --force
   ```

6. **Seed data demo (opsional)**
   ```bash
   php artisan db:seed --class=DemoDataSeeder
   ```

7. **Build assets**
   ```bash
   npm run build
   ```

8. **Jalankan development server**
   ```bash
   npm run dev
   # atau
   php artisan serve
   ```

9. **Akses aplikasi**
   - Buka: http://localhost:8000
   - Login dengan kredensial default (jika ada seeder)

## 🏗️ Arsitektur Sistem

### Single-Store Architecture
Sistem ini dirancang untuk satu toko/toko tunggal dengan arsitektur sederhana namun powerful:

```
Toko (Single Store)
├── Products (katalog produk)
├── Sales (transaksi penjualan)
├── Shifts (shift kerja kasir)
├── Cash Flows (pemasukan/pengeluaran kas)
├── Stock Movements (pergerakan stok)
└── Reports (laporan gabungan)
```

**Perubahan Terbaru**: Sistem telah direfaktor untuk menghilangkan kompleksitas multi-outlet. Semua data now]):
- `business_id` dihapus dari semua tabel
- `outlet_id` dihapus dari tabel inti (sales, shifts, products, dll)
- Middleware akses outlet dihapus/disederhanakan
- Semua laporan menampilkan data keseluruhan toko

### Database Design
- **Normalized Schema** - Relasi antar tabel yang optimal
- **Soft Deletes** - Data bisa di-restore jika diperlukan
- **UUID Primary Keys** - Memberikan keamanan ekstra
- **JSON Columns** - Untuk settings fleksibel (tax, receipt)

### Service Layer Pattern
Business logic dipisahkan ke Service classes:
- `SaleService` - Proses transaksi penjualan
- `ShiftService` - Manajemen shift kasir
- `StockService` - Inventaris dan stok
- `ReportService` - Generate laporan
- `ReceiptService` - Generate struk
- `PaymentService` - Proses pembayaran

## 📱 Modul & Fitur Detail

### 1. Point of Sale (POS)
**Fitur lengkap kasir:**
- Quick Sale - Transaksi tanpa login
- Category filtering - Filter produk by kategori
- Cart management - Tambah/hapus, edit quantity
- Discounts - Diskonmanual atau otomatis
- Payment splitting - Multiple payment methods
- Change calculation - OTomatis hitung kembalian
- Receipt printing - Cetak struk thermal printer

**File utama**: `resources/views/pos/index.blade.php`

### 2. Produk & Inventaris
**Manajemen produk:**
- Category management - Kelola kategori
- Product variants - Warna, ukuran, dll
- Stock tracking - Real-time stok
- Stock adjustment - Koreksi stok manual
- Import products - Import dari Excel
- Barcode generation - Generate barcode

**Models**: `Product`, `ProductPrice`, `ProductStock`, `ProductVariant`

### 3. Transaksi Penjualan
**Proses penjualan:**
- New sale - Buat transaksi baru
- Sale items - Tambah produk ke keranjang
- Payment processing - Proses pembayaran
- Invoice generation - Buat invoice number
- Sale history - Riwayat transaksi

**Models**: `Sale`, `SaleItem`, `SalePayment`

### 4. Shift & Cash Management
**Kel
- Shift opening - Buka kas dengan nominal awal
- Shift closing - Tutup kas, rekonsiliasi
- Cash flow - Pemasukan/pengeluaran during shift
- Expected vs actual - Bandingkan kas seharusnya vs aktual

**Models**: `Shift`, `CashFlow`

### 5. Laporan
**Reports available:**
- Sales report - Penjualan by period
- Top products - Produk terlaris
- Payment summary - Ringkasan pembayaran
- Tax report - Laporan pajak
- Z-report - Laporan shift harian

**Controller**: `ReportController`  
**Service**: `ReportService`

### 6. User & Permissions
**User management:**
- Role-based access - Admin, Kasir, Manajer
- Permissions - Control akses per-modul
- Activity logging - Track user actions
- User profile - Edit profil

**Package**: Spatie Laravel Permission

## 🔄 Status Refactor (Mei 2026)

Sistem sedang dalam proses refactor untuk:

✅ **Sudah selesai:**
- Remove `business_id` dari semua tabel kecuali yang diperlukan
- Remove `outlet_id` dari sales, shifts, products, stocks
- Update semua model untuk tanpa outlet restriction
- Simplify middleware

🔄 **Proses:**
- Clean up unused code (old outlet-related)
- Update所有laporan untuk single-store

⏳ **Akan datang:**
- Improved reporting aggregation
- Better single-store optimizations

## 📝 Catatan Penting

1. **Tidak Multi-Outlet**: Sistem sekarang **hanya untuk single store**. Jika membutuhkan multi-outlet di masa depan, perlu architecture baru.
2. **Database**: Pastikan migrasi dijalankan setelah clone
3. **Seeders**: DemoDataSeeder masih bekerja, CafeProductSeeder dihapus
4. **UUID**: Semua primary key menggunakan UUID

## 🐛 Troubleshooting

**Migration errors**: Pastikan database kosong atau jalankan migrate:fresh  
**Permission denied**: `chmod -R 755 storage bootstrap/cache`  
**Composer issues**: `composer dump-autoload`  
**Asset not loading**: `npm run build` atau `npm run dev`

## 📞 Support

Untuk pertanyaan atau issues, buat GitHub issue di repository ini.

---

**Livitap POS v1.0.0** | Dibuat dengan Laravel 12
