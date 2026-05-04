<?php

return [

    /*
    |--------------------------------------------------------------------------
    | POS Configuration
    |--------------------------------------------------------------------------
    */
    'pos' => [
        // Maksimal diskon manual tanpa approval manager (dalam persentase)
        'max_discount_percent' => env('POS_MAX_DISCOUNT', 50),
        
        // Void hanya bisa dilakukan di shift yang sama
        'void_same_shift_only' => env('POS_VOID_SAME_SHIFT', true),
        
        // Izinkan stok negatif (false = prevent)
        'allow_negative_stock' => env('POS_ALLOW_NEGATIVE_STOCK', false),
        
        // Maksimalitem dikembalikan (hold)
        'max_hold_items' => env('POS_MAX_HOLD', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Stock Management
    |--------------------------------------------------------------------------
    */
    'stock' => [
        // Jam cooldown notifikasi stok minimum (jam)
        'low_stock_alert_cooldown_hours' => env('STOCK_ALERT_COOLDOWN', 24),
        
        // Auto-reorder jika stok di bawah minimum
        'auto_reorder' => env('STOCK_AUTO_REORDER', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Receipt Configuration
    |--------------------------------------------------------------------------
    */
    'receipt' => [
        // Ukuran kertas struk yang tersedia
        'paper_sizes' => ['58mm', '80mm', 'A4'],
        
        // Ukuran default
        'default_size' => env('RECEIPT_DEFAULT_SIZE', '80mm'),
        
        // Tampilkan logo di struk
        'show_logo' => env('RECEIPT_SHOW_LOGO', true),
        
        // Tampilkan pajak di struk
        'show_tax' => env('RECEIPT_SHOW_TAX', true),
        
        // Tampilkan metode pembayaran
        'show_payment_method' => env('RECEIPT_SHOW_PAYMENT', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Loyalty Program
    |--------------------------------------------------------------------------
    */
    'loyalty' => [
        // Rp X = 1 poin (misal: 1000 artinya Rp 1.000 = 1 poin)
        'points_per_rupiah' => env('LOYALTY_POINTS_PER_RUPIAH', 1000),
        
        // Nilai 1 poin dalam rupiah
        'point_value' => env('LOYALTY_POINT_VALUE', 10),
        
        // Hari hingga poin kadaluarsa (0 = tidak pernah kadaluarsa)
        'expire_days' => env('LOYALTY_EXPIRE_DAYS', 365),
        
        // Nilai minimum untuk redeem
        'min_redeem_points' => env('LOYALTY_MIN_REDEEM', 100),
        
        // Auto-award points setelah transaksi selesai
        'auto_award' => env('LOYALTY_AUTO_AWARD', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tax Settings
    |--------------------------------------------------------------------------
    */
    'tax' => [
        // Aktifkan PPN
        'enabled' => env('TAX_ENABLED', false),
        
        // Default PPN persentase (default 11% sesuai UU Indonesia)
        'default_rate' => env('TAX_DEFAULT_RATE', 11),
        
        // Pajak sudah termasuk harga (true inclusive, false exclusive)
        'inclusive' => env('TAX_INCLUSIVE', false),
        
        // Pajak per kategori produk (json: {"category_id": rate})
        'category_rates' => env('TAX_CATEGORY_RATES', '[]'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Gateway
    |--------------------------------------------------------------------------
    */
    'payment' => [
        // Enable payment gateway
        'gateway' => env('PAYMENT_GATEWAY', 'manual'), // manual, midtrans, xendit, tripay
        
        // Mode (sandbox / production)
        'mode' => env('PAYMENT_MODE', 'sandbox'),
        
        // API Keys
        'midtrans_server_key' => env('MIDTRANS_SERVER_KEY', ''),
        'midtrans_client_key' => env('MIDTRANS_CLIENT_KEY', ''),
        
        'xendit_api_key' => env('XENDIT_API_KEY', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | F&B (Food & Beverage) Settings
    |--------------------------------------------------------------------------
    */
    'fnb' => [
        // Aktifkan mode F&B
        'enabled' => env('FNB_ENABLED', false),
        
        // Auto-print ke kitchen
        'auto_print_kitchen' => env('FNB_AUTO_PRINT_KITCHEN', true),
        
        // Table charge (persentase)
        'table_charge' => env('FNB_TABLE_CHARGE', 0),
    ],

    /*
    |--------------------------------------------------------------------------
    | QR Code Settings
    |--------------------------------------------------------------------------
    */
    'qr' => [
        // Ukuran QR code untuk menu digital
        'menu_size' => env('QR_MENU_SIZE', 300),
        
        // Enable QR ordering
        'menu_ordering_enabled' => env('QR_ORDERING_ENABLED', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Role Definitions
    |--------------------------------------------------------------------------
    */
    'roles' => [
        'super_admin' => 'Super Admin',
        'owner' => 'Owner',
        'manager' => 'Manager',
        'cashier' => 'Kasir',
        'stock_keeper' => 'Gudang/Stok Keeper',
        'waiter' => 'Waiter/Pelayan',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Permission Definitions
    |--------------------------------------------------------------------------
    */
    'permissions' => [
        // POS
        'create-sale' => 'Buat Transaksi',
        'void-sale' => 'Void Transaksi',
        'hold-sale' => 'Tahan Pesanan',
        'process-refund' => 'Proses Refund',
        
        // Products
        'create-product' => 'Tambah Produk',
        'edit-product' => 'Edit Produk',
        'delete-product' => 'Hapus Produk',
        
        // Stock
        'adjust-stock' => 'Penyesuaian Stok',
        'transfer-stock' => 'Transfer Stok',
        'stock-opname' => 'Stock Opname',
        
        // Reports
        'view-sales-report' => 'Lihat Laporan Penjualan',
        'view-stock-report' => 'Lihat Laporan Stok',
        'view-financial-report' => 'Lihat Laporan Keuangan',
        
        // Users
        'manage-users' => 'Kelola User',
        'assign-outlet' => 'Assign Outlet',
        
        // Settings
        'manage-settings' => 'Kelola Pengaturan',
        'manage-discounts' => 'Kelola Diskon',
        
        // Customers
        'create-customer' => 'Tambah Pelanggan',
        'edit-customer' => 'Edit Pelanggan',
        'delete-customer' => 'Hapus Pelanggan',
    ],
];
