<?php

use App\Http\Controllers\PosController;
use App\Http\Controllers\StoreSelectionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('stores/select', [StoreSelectionController::class, 'create'])->name('stores.select');
    Route::post('stores/select', [StoreSelectionController::class, 'store'])->name('stores.select.store');
});

Route::middleware(['auth', 'store.selected', 'store.context'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::get('pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');

    Route::resource('products', \App\Http\Controllers\ProductController::class)->only(['index', 'store']);
    Route::resource('suppliers', \App\Http\Controllers\SupplierController::class)->only(['index', 'store']);
    Route::resource('customers', \App\Http\Controllers\CustomerController::class)->only(['index', 'store']);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
