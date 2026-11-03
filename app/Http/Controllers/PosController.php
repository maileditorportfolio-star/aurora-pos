<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\InvoiceService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PosController extends Controller
{
    public function index()
    {
        $storeId = session('current_store_id');

        return Inertia::render('pos/index', [
            'products' => Product::where('store_id', $storeId)
                ->with(['category', 'variants', 'stocks'])
                ->where('is_active', true)
                ->get(),
        ]);
    }

    public function checkout(Request $request, InvoiceService $invoiceService, StockService $stockService)
    {
        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.variant_id' => ['nullable', 'exists:product_variants,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'payments' => ['required', 'array'],
        ]);

        $storeId = session('current_store_id');

        return DB::transaction(function () use ($validated, $storeId, $invoiceService, $stockService) {
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);
                $lineTotal = $product->selling_price * $item['quantity'];
                $subtotal += $lineTotal;
            }

            $sale = \App\Models\Sale::create([
                'store_id' => $storeId,
                'customer_id' => $validated['customer_id'] ?? null,
                'user_id' => auth()->id(),
                'invoice_no' => $invoiceService->next('sale'),
                'status' => 'completed',
                'sale_date' => now(),
                'subtotal' => $subtotal,
                'total_amount' => $subtotal,
                'paid_amount' => $subtotal,
                'due_amount' => 0,
                'change_amount' => 0,
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                $lineTotal = $product->selling_price * $item['quantity'];
                $sale->items()->create([
                    'product_id' => $product->id,
                    'variant_id' => $item['variant_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->selling_price,
                    'line_total' => $lineTotal,
                ]);

                $stockService->adjust($product->id, $item['variant_id'] ?? null, $storeId, $item['quantity'], 'sale', Sale::class, $sale->id, 'POS sale');
            }

            return redirect()->route('pos.index')->with('success', 'Sale completed: '.$sale->invoice_no);
        });
    }
}
