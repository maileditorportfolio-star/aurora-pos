<?php

namespace App\Http\Middleware;

use App\Models\Store;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetStoreContext
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return $next($request);
        }

        $storeId = session('current_store_id');
        if ($storeId) {
            $store = Store::where('id', $storeId)->where('is_active', true)->first();
            if ($store) {
                app()->instance('current_store', $store);
                $request->attributes->set('current_store', $store);
            }
        }

        return $next($request);
    }
}
