<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Customer;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function sales(Request $request)
    {
        $from = $request->date_from ? \Carbon\Carbon::parse($request->date_from)->startOfDay() : \Carbon\Carbon::today()->startOfMonth();
        $to = $request->date_to ? \Carbon\Carbon::parse($request->date_to)->endOfDay() : \Carbon\Carbon::today()->endOfDay();

        $report = $this->reportService->salesReport($from, $to, $request->user_id);

        $sales = $report['sales'];
        $summary = $report['summary'];
        $paymentMethods = $report['payment_methods'];
        $topProducts = $report['top_products'];

        $users = \App\Models\User::all();

        return view('reports.sales', compact(
            'sales', 
            'summary', 
            'paymentMethods', 
            'topProducts',
            'users',
            'from',
            'to'
        ));
    }

    public function products(Request $request)
    {
        $report = $this->reportService->stockReport();

        $stocks = $report['stocks'];
        $summary = $report['summary'];

        return view('reports.products', compact('stocks', 'summary'));
    }

    public function stock()
    {
        $report = $this->reportService->stockReport();

        return view('reports.stock', $report);
    }

use App\Exports\SalesExport;
use Maatwebsite\Excel\Facades\Excel;

// ...

    public function export(Request $request)
    {
        $request->validate([
            'type' => 'required|in:pdf,excel',
            'report' => 'required|in:sales,products,stock',
        ]);

        $from = $request->date_from ? \Carbon\Carbon::parse($request->date_from) : null;
        $to = $request->date_to ? \Carbon\Carbon::parse($request->date_to) : null;
        $userId = $request->user_id;

        if ($request->type === 'excel' && $request->report === 'sales') {
            return Excel::download(new SalesExport($from, $to, $userId), 'laporan-penjualan-' . now()->format('Y-m-d') . '.xlsx');
        }

        return back()->with('error', 'Fitur ekspor belum tersedia untuk format/laporan ini');
    }

    public function dashboard()
    {
        $summary = $this->reportService->dashboardSummary();

        return view('dashboard', $summary);
    }
}
