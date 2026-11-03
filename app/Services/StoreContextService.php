<?php

namespace App\Services;

use App\Models\Store;
use Illuminate\Support\Facades\Auth;

class StoreContextService
{
    public function current(): ?Store
    {
        if (app()->has('current_store')) {
            return app('current_store');
        }

        $storeId = session('current_store_id');
        if (! $storeId || ! Auth::check()) {
            return null;
        }

        $store = Store::find($storeId);
        if ($store) {
            app()->instance('current_store', $store);
        }

        return $store;
    }

    public function id(): ?int
    {
        return $this->current()?->id;
    }
}
