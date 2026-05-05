<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Struk #{{ $sale->invoice_number }}</title>
    <style>
        body { font-family: 'Courier New', monospace; font-size: 12px; width: 80mm; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 5px 0; font-size: 16px; }
        .info { margin-bottom: 15px; font-size: 11px; }
        .items { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .items th { text-align: left; border-bottom: 1px solid #000; }
        .items td { padding: 3px 0; }
        .summary { border-top: 2px dashed #000; padding-top: 10px; }
        .footer { text-align: center; margin-top: 20px; font-size: 10px; }
    </style>
</head>
<body onload="printAndRedirect()">
    <script>
        function printAndRedirect() {
            window.print();
            window.location.href = "{{ route('pos.index') }}";
        }
    </script>
    <div class="header">
        <h2>LIVITAP POS</h2>
        <p>{{ session('business_address') ?? 'Alamat bisnis' }}</p>
        <p>Telp: {{ session('business_phone') ?? '' }}</p>
    </div>

    <div class="info">
        <p>No. Struk: {{ $sale->invoice_number }}</p>
        <p>Tanggal: {{ $sale->sale_date->format('d/m/Y H:i:s') }}</p>
        <p>Kasir: {{ $sale->user->name ?? '-' }}</p>
        @if($sale->customer)
            <p>Pelanggan: {{ $sale->customer->name }}</p>
        @endif
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>Item</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Harga</th>
                <th class="text-right">Subtl</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
            <tr>
                <td>{{ $item->name_snapshot }}</td>
                <td class="text-right">{{ $item->qty }}</td>
                <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <div style="display: flex; justify-content: space-between;">
            <span>Subtotal:</span>
            <span>Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</span>
        </div>
        @if($sale->discount_amount > 0)
        <div style="display: flex; justify-content: space-between;">
            <span>Diskon:</span>
            <span>Rp {{ number_format($sale->discount_amount, 0, ',', '.') }}</span>
        </div>
        @endif
        @if($sale->tax_amount > 0)
        <div style="display: flex; justify-content: space-between;">
            <span>Pajak:</span>
            <span>Rp {{ number_format($sale->tax_amount, 0, ',', '.') }}</span>
        </div>
        @endif
        <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 14px; margin-top: 5px;">
            <span>TOTAL:</span>
            <span>Rp {{ number_format($sale->total, 0, ',', '.') }}</span>
        </div>
        <div style="display: flex; justify-content: space-between;">
            <span>Tunai:</span>
            <span>Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</span>
        </div>
        <div style="display: flex; justify-content: space-between;">
            <span>Kembali:</span>
            <span>Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</span>
        </div>

        @if($sale->payments->count() > 0)
        <div style="margin-top: 10px; border-top: 1px dotted #000; padding-top: 5px;">
            <p><strong>Pembayaran:</strong></p>
            @foreach($sale->payments as $payment)
                <p>{{ $payment->method }}: Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
            @endforeach
        </div>
        @endif
    </div>

    @if($sale->notes)
    <div style="margin-top: 10px; font-style: italic;">
        Catatan: {{ $sale->notes }}
    </div>
    @endif

    <div class="footer">
        <p>Terima kasih atas kunjungan Anda!</p>
        <p>{{ session('business_npwp') ?? '' }}</p>
    </div>
</body>
</html>