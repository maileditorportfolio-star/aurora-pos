<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StoreSelectionController extends Controller
{
    public function create(Request $request): Response
    {
        return Inertia::render('stores/select', [
            'stores' => $request->user()->stores()->where('is_active', true)->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'store_id' => ['required', 'exists:stores,id'],
        ]);

        $user = $request->user();
        if (! $user->stores()->where('stores.id', $request->store_id)->exists()) {
            abort(403);
        }

        session(['current_store_id' => $request->store_id]);

        return redirect()->intended(route('dashboard'));
    }
}
