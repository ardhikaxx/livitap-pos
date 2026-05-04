<?php

namespace App\Services;

use App\Models\Discount;
use App\Models\Voucher;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DiscountService
{
    /**
     * Hitung diskon untuk keranjang
     */
    public function calculateDiscount($cart, $customerId = null, $outletId = null)
    {
        $subtotal = collect($cart)->sum(function ($item) {
            return $item['price'] * $item['qty'];
        });

        $bestDiscount = $this->findBestDiscount($subtotal, $cart, $customerId, $outletId);
        
        return [
            'discount_amount' => $bestDiscount['amount'],
            'discount_type' => $bestDiscount['type'],
            'discount_applied' => $bestDiscount['discount'] ?? null,
        ];
    }

    /**
     * Cari diskon terbaik yang berlaku
     */
    private function findBestDiscount($subtotal, $cart, $customerId, $outletId)
    {
        $now = Carbon::now();
        $applicableDiscounts = [];

        // Diskon otomatis (promo aktif)
        $promos = Discount::where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', $now);
            })
            ->where('min_purchase', '<=', $subtotal)
            ->get();

        foreach ($promos as $discount) {
            $amount = $this->calculateDiscountValue($discount, $cart, $subtotal);
            if ($amount > 0) {
                $applicableDiscounts[] = [
                    'amount' => $amount,
                    'type' => 'auto',
                    'discount' => $discount,
                ];
            }
        }

        // Voucher jika ada
        if (request()->has('voucher_code')) {
            $voucher = Voucher::where('code', request('voucher_code'))
                ->where('is_active', true)
                ->where(function ($q) use ($now) {
                    $q->whereNull('expires_at')
                      ->orWhere('expires_at', '>=', $now);
                })
                ->whereDoesntHave('usedBy', function ($q) {
                    $q->where('used_at', '!=', null);
                })
                ->first();

            if ($voucher && $voucher->discount->is_active) {
                $amount = $this->calculateDiscountValue($voucher->discount, $cart, $subtotal);
                if ($amount > 0) {
                    $applicableDiscounts[] = [
                        'amount' => $amount,
                        'type' => 'voucher',
                        'discount' => $voucher->discount,
                        'voucher' => $voucher,
                    ];
                }
            }
        }

        // Pilih yang paling menguntungkan pelanggan
        if (empty($applicableDiscounts)) {
            return ['amount' => 0, 'type' => null];
        }

        usort($applicableDiscounts, function ($a, $b) {
            return $b['amount'] <=> $a['amount'];
        });

        return $applicableDiscounts[0];
    }

    /**
     * Hitung nilai diskon berdasarkan tipe
     */
    private function calculateDiscountValue($discount, $cart, $subtotal)
    {
        if ($discount->type === 'percentage') {
            $amount = $subtotal * ($discount->value / 100);
        } elseif ($discount->type === 'nominal') {
            $amount = $discount->value;
        } elseif ($discount->type === 'bogo') {
            // Buy X Get Y - bisa kompleks, sederhanakan dulu
            $amount = 0; 
        } else {
            $amount = 0;
        }

        // Batasi maksimal diskon
        if ($discount->max_discount && $amount > $discount->max_discount) {
            $amount = $discount->max_discount;
        }

        // Jangan sampai negatif
        return max(0, $amount);
    }

    /**
     * Validasi & apply voucher
     */
    public function applyVoucher($code, $saleId)
    {
        $voucher = Voucher::where('code', $code)->first();
        
        if (!$voucher || !$voucher->is_active) {
            throw new \Exception("Voucher tidak valid");
        }

        if ($voucher->expires_at && $voucher->expires_at < now()) {
            throw new \Exception("Voucher sudah kadaluarsa");
        }

        if ($voucher->used_by) {
            throw new \Exception("Voucher sudah digunakan");
        }

        // Cek usage limit
        if ($voucher->discount->usage_limit && 
            $voucher->discount->used_count >= $voucher->discount->usage_limit) {
            throw new \Exception("Voucher sudah mencapai batas penggunaan");
        }

        // Mark sebagai used
        $voucher->update([
            'used_by' => auth()->id(),
            'used_at' => now(),
        ]);

        // Increment used_count
        $voucher->discount->increment('used_count');

        return $voucher;
    }
}
