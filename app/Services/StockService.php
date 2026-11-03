<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function adjust($productId, $variantId, $storeId, $qty, $type, $refType = null, $refId = null, $note = null, $unitCost = null): void
    {
        DB::transaction(function () use ($productId, $variantId, $storeId, $qty, $type, $refType, $refId, $note, $unitCost) {
            $stock = Stock::where('store_id', $storeId)
                ->where('product_id', $productId)
                ->when($variantId, fn ($q) => $q->where('variant_id', $variantId), fn ($q) => $q->whereNull('variant_id'))
                ->lockForUpdate()
                ->firstOrCreate(
                    ['store_id' => $storeId, 'product_id' => $productId, 'variant_id' => $variantId],
                    ['quantity' => 0, 'reserved_quantity' => 0]
                );

            if (in_array($type, ['purchase', 'adjust_in', 'return_in'])) {
                $stock->quantity += abs($qty);
            } else {
                $stock->quantity -= abs($qty);
            }

            $stock->save();

            StockMovement::create([
                'store_id' => $storeId,
                'product_id' => $productId,
                'variant_id' => $variantId,
                'type' => $type,
                'quantity' => $qty,
                'balance_after' => $stock->quantity,
                'reference_type' => $refType,
                'reference_id' => $refId,
                'note' => $note,
                'unit_cost' => $unitCost,
                'created_by' => auth()->id(),
            ]);
        });
    }
}
