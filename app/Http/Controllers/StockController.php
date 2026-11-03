<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stock;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StockController extends Controller
{
    public function index()
    {
        $storeId = session('current_store_id');

        return Inertia::render('inventory/index', [
            'stocks' => Stock::where('store_id', $storeId)->with(['product'])->get(),
            'products' => Product::where('store_id', $storeId)->where('is_active', true)->get(),
        ]);
    }

    public function adjust(Request $request, StockService $stockService)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'numeric'],
            'type' => ['required', 'in:adjust_in,adjust_out'],
            'note' => ['nullable', 'string'],
        ]);

        $storeId = session('current_store_id');

        DB::transaction(function () use ($validated, $storeId, $stockService) {
            $stockService->adjust($validated['product_id'], null, $storeId, $validated['quantity'], $validated['type'], null, null, $validated['note'] ?? null);
        });

        return redirect()->back()->with('success', 'Stock adjusted');
    }
}
