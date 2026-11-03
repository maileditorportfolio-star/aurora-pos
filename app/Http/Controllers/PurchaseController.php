<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\InvoiceService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PurchaseController extends Controller
{
    public function index()
    {
        $storeId = session('current_store_id');

        return Inertia::render('purchases/index', [
            'purchases' => Purchase::where('store_id', $storeId)->with(['supplier'])->latest()->get(),
        ]);
    }

    public function create()
    {
        $storeId = session('current_store_id');

        return Inertia::render('purchases/create', [
            'suppliers' => Supplier::where('store_id', $storeId)->get(),
            'products' => Product::where('store_id', $storeId)->where('is_active', true)->get(),
        ]);
    }

    public function store(Request $request, InvoiceService $invoiceService, StockService $stockService)
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
        ]);

        $storeId = session('current_store_id');

        return DB::transaction(function () use ($validated, $storeId, $invoiceService, $stockService) {
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += $item['quantity'] * $item['unit_cost'];
            }

            $purchase = Purchase::create([
                'store_id' => $storeId,
                'supplier_id' => $validated['supplier_id'],
                'purchase_no' => $invoiceService->next('purchase'),
                'status' => 'received',
                'purchase_date' => $validated['purchase_date'],
                'subtotal' => $subtotal,
                'total_amount' => $subtotal,
                'paid_amount' => 0,
                'due_amount' => $subtotal,
                'created_by' => auth()->id(),
                'received_at' => now(),
            ]);

            foreach ($validated['items'] as $item) {
                $purchase->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'quantity_received' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'line_total' => $item['quantity'] * $item['unit_cost'],
                ]);

                $stockService->adjust($item['product_id'], null, $storeId, $item['quantity'], 'purchase', Purchase::class, $purchase->id, 'Purchase received');
            }

            return redirect()->route('purchases.index')->with('success', 'Purchase created: '.$purchase->purchase_no);
        });
    }
}
