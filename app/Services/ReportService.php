<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Shift;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportService
{
    /**
     * Laporan penjualan berdasarkan periode
     */
    public function salesReport($from, $to, $outletId = null, $userId = null)
    {
        $query = Sale::with(['user', 'items.product'])
            ->whereBetween('sale_date', [$from, $to])
            ->where('status', 'paid');

        if ($outletId) {
            $query->where('outlet_id', $outletId);
        }
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $sales = $query->orderBy('sale_date', 'desc')->paginate(20)->appends(request()->except('page'));
        
        $summaryQuery = Sale::whereBetween('sale_date', [$from, $to])
            ->where('status', 'paid');
        if ($outletId) {
            $summaryQuery->where('outlet_id', $outletId);
        }
        if ($userId) {
            $summaryQuery->where('user_id', $userId);
        }
        $allSales = $summaryQuery->with('items')->get();
        
        $totalSales = $allSales->sum('total');
        $totalItems = $allSales->sum(function ($sale) {
            return $sale->items->sum('qty');
        });

        $paymentMethods = $allSales->flatMap->payments()
            ->groupBy('method')
            ->map(function ($payments) {
                return $payments->sum('amount');
            });

        $topProducts = SaleItem::select('product_id', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(subtotal) as total_sales'))
            ->whereIn('sale_id', $allSales->pluck('id'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        return [
            'sales' => $sales,
            'summary' => [
                'total_sales' => $totalSales,
                'total_transactions' => $sales->count(),
                'total_items' => $totalItems,
                'average_per_transaction' => $sales->count() > 0 ? $totalSales / $sales->count() : 0,
            ],
            'payment_methods' => $paymentMethods,
            'top_products' => $topProducts,
        ];
    }

    /**
     * Laporan stok
     */
    public function stockReport($outletId = null)
    {
        $query = \App\Models\ProductStock::with(['product', 'outlet'])
            ->when($outletId, function ($q) use ($outletId) {
                return $q->where('outlet_id', $outletId);
            });

        $stocks = $query->get();
        
        $totalValue = $stocks->sum(function ($stock) {
            return $stock->qty * ($stock->product->prices->first()?->buy_price ?? 0);
        });

        $lowStock = $stocks->filter(function ($stock) {
            return $stock->qty <= $stock->min_qty && $stock->min_qty > 0;
        });

        $movements = StockMovement::with(['product', 'user'])
            ->when($outletId, function ($q) use ($outletId) {
                return $q->where('outlet_id', $outletId);
            })
            ->whereDate('created_at', Carbon::today())
            ->get();

        return [
            'stocks' => $stocks,
            'summary' => [
                'total_value' => $totalValue,
                'total_products' => $stocks->count(),
                'low_stock_count' => $lowStock->count(),
                'today_movements' => $movements->count(),
            ],
            'low_stock' => $lowStock,
            'today_movements' => $movements,
        ];
    }

    /**
     * Laporan shift kasir
     */
    public function shiftReport(Shift $shift)
    {
        $shift->load(['user', 'sales.items.product', 'cashFlows']);
        
        $totalSales = $shift->sales->sum('total');
        $totalTransactions = $shift->sales->count();
        $averageTicket = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;

        $paymentBreakdown = $shift->sales->flatMap->payments()
            ->groupBy('method')
            ->map(function ($payments) {
                return $payments->sum('amount');
            });

        return [
            'shift' => $shift,
            'summary' => [
                'total_sales' => $totalSales,
                'total_transactions' => $totalTransactions,
                'average_ticket' => $averageTicket,
                'opening_cash' => $shift->opening_cash,
                'closing_cash' => $shift->closing_cash,
                'expected_cash' => $shift->expected_cash,
                'difference' => $shift->difference,
            ],
            'payment_breakdown' => $paymentBreakdown,
            'sales' => $shift->sales,
            'cash_flows' => $shift->cashFlows,
        ];
    }

    /**
     * Dashboard summary untuk hari ini
     */
    public function dashboardSummary($outletId, $date = null)
    {
        $date = $date ?: Carbon::today();
        
        $todaySales = Sale::where('outlet_id', $outletId)
            ->whereDate('sale_date', $date)
            ->where('status', 'paid')
            ->get();

        $yesterdaySales = Sale::where('outlet_id', $outletId)
            ->whereDate('sale_date', Carbon::yesterday())
            ->where('status', 'paid')
            ->get();

        $activeShift = Shift::where('outlet_id', $outletId)
            ->where('status', 'open')
            ->first();

        return [
            'today_sales' => $todaySales->sum('total'),
            'today_transactions' => $todaySales->count(),
            'yesterday_sales' => $yesterdaySales->sum('total'),
            'growth' => $yesterdaySales->sum('total') > 0 
                ? (($todaySales->sum('total') - $yesterdaySales->sum('total')) / $yesterdaySales->sum('total')) * 100 
                : 0,
            'active_shift' => $activeShift,
            'top_product_today' => $this->getTopProductToday($outletId, $date),
        ];
    }

    private function getTopProductToday($outletId, $date)
    {
        return SaleItem::select('product_id', DB::raw('SUM(qty) as total_qty'))
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.outlet_id', $outletId)
            ->whereDate('sales.sale_date', $date)
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->first();
    }
}
