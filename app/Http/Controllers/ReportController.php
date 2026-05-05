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

    public function export(Request $request)
    {
        $request->validate([
            'type' => 'required|in:pdf,excel',
            'report' => 'required|in:sales,products,stock',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        $reportType = $request->report;
        $data = [];

        switch ($reportType) {
            case 'sales':
                $from = $request->date_from ? \Carbon\Carbon::parse($request->date_from) : \Carbon\Carbon::today()->startOfMonth();
                $to = $request->date_to ? \Carbon\Carbon::parse($request->date_to) : \Carbon\Carbon::today();
                $report = $this->reportService->salesReport($from, $to);
                $data = $report['sales'];
                break;
            case 'stock':
                $report = $this->reportService->stockReport();
                $data = $report['stocks'];
                break;
        }

        return back()->with('success', 'Export berhasil (demo)');
    }

    public function dashboard()
    {
        $summary = $this->reportService->dashboardSummary();

        return view('dashboard', $summary);
    }
}
