<?php

namespace App\Services;

use App\Models\InvoiceSequence;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function next(string $type): string
    {
        $storeId = app(StoreContextService::class)->id();
        if (! $storeId) {
            throw new \Exception('No store context');
        }

        return DB::transaction(function () use ($storeId, $type) {
            $seq = InvoiceSequence::where('store_id', $storeId)->where('type', $type)
                ->lockForUpdate()
                ->firstOrCreate(
                    ['store_id' => $storeId, 'type' => $type],
                    ['prefix' => strtoupper($type).'/', 'current_number' => 0, 'padding' => 6]
                );

            $seq->current_number++;
            $seq->save();

            return $seq->prefix.str_pad((string) $seq->current_number, $seq->padding, '0', STR_PAD_LEFT);
        });
    }
}
