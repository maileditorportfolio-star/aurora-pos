<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStoreSelected
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();
        if (! $user->stores()->exists()) {
            return redirect()->route('stores.select');
        }

        if (! session()->has('current_store_id')) {
            $default = $user->defaultStore();
            $store = $default ?: $user->stores()->first();
            if ($store) {
                session(['current_store_id' => $store->id]);
                $request->attributes->set('current_store_id', $store->id);
            }
        }

        return $next($request);
    }
}
