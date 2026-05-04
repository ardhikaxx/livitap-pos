<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $sale->invoice_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white">
    <div class="max-w-md mx-auto p-4" id="receipt">
        <div class="text-center mb-4">
            <h2 class="font-bold text-lg">{{ $sale->outlet->business->name ?? 'LIVITAP POS' }}</h2>
            <p class="text-sm">{{ $sale->outlet->address ?? '' }}</p>
        </div>
        
        <div class="border-t border-b py-2 mb-2">
            <div class="flex justify-between text-sm">
                <span>No. Struk:</span>
                <span>{{ $sale->invoice_number }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span>Tanggal:</span>
                <span>{{ $sale->sale_date->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span>Kasir:</span>
                <span>{{ $sale->user->name }}</span>
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
                @foreach($sale->items as $item)
                    <tr>
                        <td>
                            {{ $item->name_snapshot }}<br>
                            <span class="text-xs">{{ $item->qty }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                        </td>
                        <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="border-t pt-2">
            <div class="flex justify-between font-bold">
                <span>Total:</span>
                <span>Rp {{ number_format($sale->total, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Bayar:</span>
                <span>Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Kembali:</span>
                <span>Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</span>
            </div>
        </div>
        
        <div class="text-center mt-4 text-xs">
            <p>Terima kasih atas kunjungan Anda!</p>
        </div>
    </div>
    
    <div class="text-center mt-4 print:hidden">
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded">Print Struk</button>
        <a href="{{ route('pos.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded">Transaksi Baru</a>
    </div>
    
    <script>
        window.onload = function() {
            setTimeout(function() { window.print(); }, 500);
        }
    </script>
    
    <style>
        @media print {
            .print\\:hidden { display: none !important; }
        }
    </style>
</body>
</html>