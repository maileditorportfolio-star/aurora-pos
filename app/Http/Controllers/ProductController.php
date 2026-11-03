<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        $storeId = session('current_store_id');

        return Inertia::render('products/index', [
            'products' => Product::where('store_id', $storeId)->with(['category', 'brand'])->get(),
            'categories' => Category::where('store_id', $storeId)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'sku' => ['required', 'string', 'max:100'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $validated['store_id'] = session('current_store_id');
        $validated['slug'] = str()->slug($validated['name']).'-'.str()->random(6);

        Product::create($validated);

        return redirect()->back()->with('success', 'Product created');
    }
}
