<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::with(['business'])
            ->where('is_active', true);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('phone', 'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%');
            });
        }

        $customers = $query->paginate(25);
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        $businesses = \App\Models\Business::all();
        return view('customers.create', compact('businesses'));
    }

    public function store(StoreCustomerRequest $request)
    {
        $customer = Customer::create($request->validated());
        
        return redirect()->route('customers.index')
            ->with('success', 'Pelanggan berhasil ditambahkan');
    }

    public function show(Customer $customer)
    {
        $customer->load(['sales' => function ($q) {
            $q->latest()->take(10);
        }, 'pointTransactions' => function ($q) {
            $q->latest()->take(20);
        }]);

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $businesses = \App\Models\Business::all();
        return view('customers.edit', compact('customer', 'businesses'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());
        
        return redirect()->route('customers.index')
            ->with('success', 'Data pelanggan berhasil diperbarui');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return back()->with('success', 'Pelanggan dihapus');
    }

    public function transactions(Customer $customer)
    {
        $transactions = $customer->sales()
            ->with(['items.product', 'payments'])
            ->latest()
            ->paginate(20);

        return view('customers.transactions', compact('customer', 'transactions'));
    }

    public function points(Customer $customer)
    {
        $pointHistory = $customer->pointTransactions()->latest()->paginate(20);
        return view('customers.points', compact('customer', 'pointHistory'));
    }
}
