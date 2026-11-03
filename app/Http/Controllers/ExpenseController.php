<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExpenseController extends Controller
{
    public function index()
    {
        $storeId = session('current_store_id');

        return Inertia::render('expenses/index', [
            'expenses' => Expense::where('store_id', $storeId)->with(['category'])->latest()->get(),
            'categories' => ExpenseCategory::where('store_id', $storeId)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_category_id' => ['nullable', 'exists:expense_categories,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'expense_date' => ['required', 'date'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['store_id'] = session('current_store_id');
        $validated['expense_no'] = 'EXP-'.strtoupper(str()->random(8));
        $validated['created_by'] = auth()->id();

        Expense::create($validated);

        return redirect()->back()->with('success', 'Expense created');
    }
}
