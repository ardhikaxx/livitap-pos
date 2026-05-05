<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::withCount('sales')
            ->withSum('sales', 'total')
            ->with(['sales' => function($q) {
                $q->latest()->limit(1);
            }])
            ->latest()
            ->paginate(20);

        return view('customers.index', compact('customers'));
    }
}
