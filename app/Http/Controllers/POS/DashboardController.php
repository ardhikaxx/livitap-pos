<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Shift;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'prices'])->where('is_active', true)->where('is_pos_visible', true)->get();
        
        $categories = Category::all();
        
        $activeShift = Shift::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        return view('pos.index', compact('products', 'categories', 'activeShift'));
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