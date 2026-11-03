<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupplierController extends Controller
{
    public function index()
    {
        $storeId = session('current_store_id');

        return Inertia::render('suppliers/index', [
            'suppliers' => Supplier::where('store_id', $storeId)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
        ]);

        $validated['store_id'] = session('current_store_id');
        Supplier::create($validated);

        return redirect()->back()->with('success', 'Supplier created');
    }
}
