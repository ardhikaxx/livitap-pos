<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Shift;
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
        $outletId = session('outlet_id');

        $report = $this->reportService->salesReport($from, $to, $outletId, $request->user_id);

        $sales = $report['sales'];
        $summary = $report['summary'];
        $paymentMethods = $report['payment_methods'];
        $topProducts = $report['top_products'];

        $users = \App\Models\User::where('business_id', auth()->user()->business_id)->get();

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
        $outletId = session('outlet_id');
        $report = $this->reportService->stockReport($outletId);

        $stocks = $report['stocks'];
        $summary = $report['summary'];

        return view('reports.products', compact('stocks', 'summary'));
    }

    public function stock()
    {
        $outletId = session('outlet_id');
        $report = $this->reportService->stockReport($outletId);

        return view('reports.stock', $report);
    }

    public function cashier(Request $request)
    {
        $from = $request->date_from ? \Carbon\Carbon::parse($request->date_from) : \Carbon\Carbon::today()->startOfMonth();
        $to = $request->date_to ? \Carbon\Carbon::parse($request->date_to) : \Carbon\Carbon::today();

        $shifts = Shift::with(['user', 'sales', 'cashFlows'])
            ->where('outlet_id', session('outlet_id'))
            ->whereBetween('opened_at', [$from, $to])
            ->orderBy('opened_at', 'desc')
            ->paginate(20);

        return view('reports.cashier', compact('shifts', 'from', 'to'));
    }

    public function shift(Shift $shift)
    {
        $report = $this->reportService->shiftReport($shift);
        
        return view('reports.shift', [
            'shift' => $report['shift'],
            'summary' => $report['summary'],
            'payment_breakdown' => $report['payment_breakdown'],
            'sales' => $report['sales'],
            'cash_flows' => $report['cash_flows'],
        ]);
    }

    public function export(Request $request)
    {
        $request->validate([
            'type' => 'required|in:pdf,excel',
            'report' => 'required|in:sales,products,stock,cashier',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        $reportType = $request->report;
        $data = [];

        switch ($reportType) {
            case 'sales':
                $from = $request->date_from ? \Carbon\Carbon::parse($request->date_from) : \Carbon\Carbon::today()->startOfMonth();
                $to = $request->date_to ? \Carbon\Carbon::parse($request->date_to) : \Carbon\Carbon::today();
                $report = $this->reportService->salesReport($from, $to, session('outlet_id'));
                $data = $report['sales'];
                break;
            case 'stock':
                $report = $this->reportService->stockReport(session('outlet_id'));
                $data = $report['stocks'];
                break;
        }

        if ($request->type === 'excel') {
            // Export dengan Maatwebsite Excel
            // return Excel::raw(new CustomExport($data), \Maatwebsite\Excel\Excel::XLSX);
        } else {
            // Export PDF dengan DomPDF
            // return PDF::loadView('reports.'.$reportType, compact('data'))->download();
        }

        return back()->with('success', 'Export berhasil (demo)');
    }

    public function dashboard()
    {
        $outletId = session('outlet_id');
        $summary = $this->reportService->dashboardSummary($outletId);

        return view('dashboard', $summary);
    }
}
