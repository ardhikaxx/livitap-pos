<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\PointTransaction;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class PointService
{
    protected $pointsPerRupiah;
    protected $pointValue;

    public function __construct()
    {
        $config = config('livitap.loyalty', []);
        $this->pointsPerRupiah = $config['points_per_rupiah'] ?? 1000; // Rp 1000 = 1 poin
        $this->pointValue = $config['point_value'] ?? 10; // 1 poin = Rp 10
    }

    /**
     * Hitung poin dari transaksi
     */
    public function calculatePoints($totalAmount)
    {
        return floor($totalAmount / $this->pointsPerRupiah);
    }

    /**
     * Berikan poin ke pelanggan setelah transaksi selesai
     */
    public function awardPoints(Customer $customer, Sale $sale)
    {
        $points = $this->calculatePoints($sale->total);
        
        if ($points <= 0) {
            return 0;
        }

        return DB::transaction(function () use ($customer, $sale, $points) {
            $balanceBefore = $customer->points;
            $customer->increment('points', $points);

            PointTransaction::create([
                'customer_id' => $customer->id,
                'sale_id' => $sale->id,
                'type' => 'earn',
                'points' => $points,
                'balance_before' => $balanceBefore,
                'balance_after' => $customer->fresh()->points,
                'notes' => "Points from sale #{$sale->invoice_number}",
            ]);

            return $points;
        });
    }

    /**
     * Redeem poin menjadi discount
     */
    public function redeemPoints(Customer $customer, $pointsToRedeem)
    {
        if ($customer->points < $pointsToRedeem) {
            throw new \Exception("Poin tidak mencukupi");
        }

        $monetaryValue = $pointsToRedeem * $this->pointValue;

        return DB::transaction(function () use ($customer, $pointsToRedeem, $monetaryValue) {
            $balanceBefore = $customer->points;
            $customer->decrement('points', $pointsToRedeem);

            PointTransaction::create([
                'customer_id' => $customer->id,
                'sale_id' => null,
                'type' => 'redeem',
                'points' => -$pointsToRedeem,
                'balance_before' => $balanceBefore,
                'balance_after' => $customer->fresh()->points,
                'notes' => "Redeemed {$pointsToRedeem} points for Rp {$monetaryValue} discount",
            ]);

            return $monetaryValue;
        });
    }

    /**
     * Return poin ketika transaksi di-void
     */
    public function voidPoints(Sale $sale)
    {
        if (!$sale->customer) {
            return;
        }

        $customer = $sale->customer;
        $pointsEarned = PointTransaction::where('sale_id', $sale->id)
            ->where('type', 'earn')
            ->first();

        if ($pointsEarned) {
            DB::transaction(function () use ($customer, $pointsEarned) {
                $customer->decrement('points', abs($pointsEarned->points));

                PointTransaction::create([
                    'customer_id' => $customer->id,
                    'sale_id' => $sale->id,
                    'type' => 'adjustment',
                    'points' => -abs($pointsEarned->points),
                    'balance_before' => $customer->fresh()->points + abs($pointsEarned->points),
                    'balance_after' => $customer->fresh()->points,
                    'notes' => "Points voided for sale #{$sale->invoice_number}",
                ]);
            });
        }
    }

    /**
     * Expire poin lama (dijalankan via scheduler)
     */
    public function expireOldPoints()
    {
        $expireDays = config('livitap.loyalty.expire_days', 365);
        $cutoffDate = Carbon::now()->subDays($expireDays);

        $customers = Customer::where('points', '>', 0)->get();
        
        foreach ($customers as $customer) {
            $earnedTransactions = $customer->pointTransactions()
                ->where('type', 'earn')
                ->orderBy('created_at')
                ->get();

            $totalPoints = $customer->points;
            foreach ($earnedTransactions as $transaction) {
                if ($totalPoints <= 0) break;

                $expiredAt = Carbon::parse($transaction->created_at)->addDays($expireDays);
                if ($expiredAt->isPast() && $transaction->points > 0) {
                    $expireAmount = min($transaction->points, $totalPoints);
                    $totalPoints -= $expireAmount;

                    PointTransaction::create([
                        'customer_id' => $customer->id,
                        'type' => 'expire',
                        'points' => -$expireAmount,
                        'balance_before' => $customer->points + $expireAmount,
                        'balance_after' => $customer->points,
                        'notes' => "Points expired from transaction #{$transaction->id}",
                    ]);
                }
            }

            if ($totalPoints != $customer->points) {
                $customer->update(['points' => $totalPoints]);
            }
        }
    }
}
