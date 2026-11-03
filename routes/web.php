<?php

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
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
