<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SalePayment;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Proses pembayaran untuk transaksi
     */
    public function processPayment(Sale $sale, array $payments)
    {
        $totalPaid = collect($payments)->sum('amount');
        
        if ($totalPaid < $sale->total) {
            throw new \Exception("Pembayaran tidak mencukupi. Kekurangan: Rp " . ($sale->total - $totalPaid));
        }

        return DB::transaction(function () use ($sale, $payments, $totalPaid) {
            // Hapus payment lama jika ada (untuk reprocess)
            $sale->payments()->delete();

            foreach ($payments as $payment) {
                SalePayment::create([
                    'sale_id' => $sale->id,
                    'method' => $payment['method'],
                    'amount' => $payment['amount'],
                    'reference_number' => $payment['reference_number'] ?? null,
                    'notes' => $payment['notes'] ?? null,
                ]);
            }

            // Update status sale
            $sale->update([
                'paid_amount' => $totalPaid,
                'change_amount' => $totalPaid - $sale->total,
                'status' => Sale::STATUS_PAID,
            ]);

            return $sale;
        });
    }

    /**
     * Validasi metode pembayaran
     */
    public function validatePaymentMethod($method, $amount)
    {
        $validMethods = ['cash', 'qris', 'transfer', 'debit', 'credit', 'ewallet', 'voucher', 'points'];

        if (!in_array($method, $validMethods)) {
            throw new \Exception("Metode pembayaran tidak valid");
        }

        // Validasi tambahan per metode
        switch ($method) {
            case 'voucher':
                // Voucher validation via DiscountService
                break;
            case 'points':
                if ($amount > 0) {
                    // Validate points balance
                }
                break;
            case 'cash':
                // Cash always valid
                break;
        }

        return true;
    }

    /**
     * Refund pembayaran
     */
    public function refundPayment(Sale $sale, $amount, $method = 'cash', $notes = null)
    {
        if ($amount > $sale->paid_amount) {
            throw new \Exception("Jumlah refund tidak boleh melebihi pembayaran");
        }

        return DB::transaction(function () use ($sale, $amount, $method, $notes) {
            // Catat refund sebagai payment negatif
            SalePayment::create([
                'sale_id' => $sale->id,
                'method' => $method,
                'amount' => -$amount,
                'notes' => $notes ?? 'Refund payment',
            ]);

            // Update status jika full refund
            if ($sale->paid_amount - $amount <= 0) {
                $sale->update(['status' => Sale::STATUS_REFUNDED]);
            }

            return $sale;
        });
    }
}
