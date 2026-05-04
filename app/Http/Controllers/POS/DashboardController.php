<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Table;
use App\Models\Shift;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'prices' => function($q) {
            $q->where('outlet_id', session('outlet_id', 1));
        }])->where('is_active', true)->where('is_pos_visible', true)->get();
        
        $categories = Category::all();
        $tables = Table::where('outlet_id', session('outlet_id', 1))->get();
        
        $activeShift = Shift::where('user_id', auth()->id())
            ->where('outlet_id', session('outlet_id', 1))
            ->where('status', 'open')
            ->first();

        return view('pos.index', compact('products', 'categories', 'tables', 'activeShift'));
    }

    public function getProduct(Product $product)
    {
        $price = $product->prices->first();
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'price' => $price?->sell_price ?? 0,
            'description' => $product->description,
        ]);
    }
}