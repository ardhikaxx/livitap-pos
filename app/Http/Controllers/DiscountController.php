<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Voucher;
use App\Http\Requests\StoreDiscountRequest;
use App\Http\Requests\UpdateDiscountRequest;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function index(Request $request)
    {
        $discounts = Discount::with('business')
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%');
            })
            ->latest()
            ->paginate(25);

        return view('discounts.index', compact('discounts'));
    }

    public function create()
    {
        $businesses = \App\Models\Business::all();
        $categories = \App\Models\Category::all();
        $products = \App\Models\Product::all();
        return view('discounts.create', compact('businesses', 'categories', 'products'));
    }

    public function store(StoreDiscountRequest $request)
    {
        $discount = Discount::create($request->validated());

        // Generate vouchers jika ada quantity
        if ($request->has('generate_vouchers') && $request->generate_vouchers > 0) {
            $this->generateVouchers($discount, $request->generate_vouchers);
        }

        return redirect()->route('discounts.index')
            ->with('success', 'Diskon berhasil dibuat');
    }

    public function edit(Discount $discount)
    {
        $businesses = \App\Models\Business::all();
        $categories = \App\Models\Category::all();
        $products = \App\Models\Product::all();
        return view('discounts.edit', compact('discount', 'businesses', 'categories', 'products'));
    }

    public function update(UpdateDiscountRequest $request, Discount $discount)
    {
        $discount->update($request->validated());
        
        return redirect()->route('discounts.index')
            ->with('success', 'Diskon berhasil diperbarui');
    }

    public function destroy(Discount $discount)
    {
        $discount->delete();
        return back()->with('success', 'Diskon dihapus');
    }

    /**
     * Validate voucher code
     */
    public function validateVoucher(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'sale_id' => 'nullable|exists:sales,id',
        ]);

        $voucher = Voucher::where('code', $request->code)
            ->where('is_active', true)
            ->first();

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher tidak valid',
            ], 404);
        }

        if ($voucher->expires_at && $voucher->expires_at < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher sudah kadaluarsa',
            ], 404);
        }

        if ($voucher->used_by) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher sudah digunakan',
            ], 404);
        }

        // Check usage limit
        if ($voucher->discount->usage_limit && 
            $voucher->discount->used_count >= $voucher->discount->usage_limit) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher sudah mencapai batas penggunaan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'voucher' => $voucher,
                'discount' => $voucher->discount,
            ],
        ]);
    }

    /**
     * Generate voucher codes
     */
    private function generateVouchers($discount, $quantity)
    {
        for ($i = 0; $i < $quantity; $i++) {
            Voucher::create([
                'discount_id' => $discount->id,
                'code' => strtoupper(Str::random(10)),
                'expires_at' => $discount->end_date ?? null,
            ]);
        }
    }
}
