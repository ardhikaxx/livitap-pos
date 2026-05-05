<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Sale::with(['user', 'customer', 'payments'])
            ->latest()
            ->paginate(20);

        return view('pos.transactions.index', compact('transactions'));
    }
}
