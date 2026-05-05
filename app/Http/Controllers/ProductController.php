<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Business;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;

class ProductController extends Controller
{
    /**
     * Display listing of products
     */
    public function index(Request $request)
    {
        $businessId = session('business_id');

        $query = Product::with(['category', 'prices', 'stocks'])
            ->when($request->filled('search'), fn($q) => $q
                ->where(fn($sub) => $sub
                    ->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('sku', 'like', '%'.$request->search.'%')
                    ->orWhere('barcode', 'like', '%'.$request->search.'%')
                )
            )
            ->when($request->filled('category_id'), fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->filled('is_active'),   fn($q) => $q->where('is_active', $request->is_active));

        $products   = $query->orderBy('name')->paginate(25);
        $categories = Category::all();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    /**
     * Store new product
     */
    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        $buyPrice  = $validated['buy_price'] ?? 0;
        $sellPrice = $validated['sell_price'] ?? 0;
        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']) . '-' . uniqid();
        unset($validated['buy_price'], $validated['sell_price']);

        $product = Product::create($validated);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('products/photos', 'public');
            $product->update(['photo' => $path]);
        }

        $product->prices()->create([
            'buy_price'  => $buyPrice,
            'sell_price' => $sellPrice,
        ]);

        $product->stocks()->create([
            'qty'       => 0,
            'min_qty'   => $validated['min_stock'] ?? 0,
        ]);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan');
    }

    /**
     * Show single product
     */
    public function show(Product $product)
    {
        $product->load([
            'category',
            'prices',
            'stocks',
            'variants',
            'saleItems'
        ]);
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        $product->load([
            'prices',
            'stocks',
        ]);
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update product
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $validated = $request->validated();
        
        // Extract price data
        $buyPrice = $validated['buy_price'] ?? null;
        $sellPrice = $validated['sell_price'] ?? null;
        unset($validated['buy_price'], $validated['sell_price']);

        $product->update($validated);

        // Update prices if provided
        if ($buyPrice !== null || $sellPrice !== null) {
            $updateData = [];
            if ($buyPrice !== null) $updateData['buy_price'] = $buyPrice;
            if ($sellPrice !== null) $updateData['sell_price'] = $sellPrice;

            $product->prices()->updateOrCreate(
                [],
                $updateData
            );
        }

        // Update photo if provided
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('products/photos', 'public');
            $product->update(['photo' => $path]);
        }

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui');
    }

    /**
     * Delete product
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);
        $product->delete();

        return back()->with('success', 'Produk dihapus');
    }

    /**
     * API: Search products for POS
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:1',
        ]);

        $query = $request->get('q');

        $products = Product::with(['category', 'prices'])
            ->where('is_active', true)
            ->where('is_pos_visible', true)
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%")
                  ->orWhere('barcode', 'like', "%{$query}%");
            })
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products->map(function ($product) {
                $price = $product->prices->first();
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'barcode' => $product->barcode,
                    'price' => $price?->sell_price ?? 0,
                    'unit' => $product->unit,
                    'track_stock' => $product->track_stock,
                    'has_variant' => $product->has_variant,
                ];
            }),
        ]);
    }

    /**
     * Import produk dari Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,ods',
        ]);

        Excel::import(new ProductsImport, $request->file('file'));

        return back()->with('success', 'Produk berhasil diimport');
    }
}
