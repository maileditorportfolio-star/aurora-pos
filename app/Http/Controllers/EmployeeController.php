<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    public function index()
    {
        $storeId = session('current_store_id');

        return Inertia::render('employees/index', [
            'employees' => Employee::where('store_id', $storeId)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'designation' => ['required', 'string', 'max:100'],
            'joining_date' => ['required', 'date'],
            'basic_salary' => ['required', 'numeric', 'min:0'],
        ]);

        $validated['store_id'] = session('current_store_id');
        $validated['employee_code'] = 'EMP-'.strtoupper(str()->random(8));
        $validated['created_by'] = auth()->id();

        Employee::create($validated);

        return redirect()->back()->with('success', 'Employee created');
    }
}
