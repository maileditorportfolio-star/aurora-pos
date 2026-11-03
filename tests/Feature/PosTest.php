<?php

use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Vite;

beforeEach(function () {
    Vite::useHotFile(storage_path('framework/testing/hot'));
    Vite::useBuildDirectory('build');
});

test('authenticated user with store can access pos', function () {
    $this->withoutVite();
    $user = User::factory()->create();
    $store = Store::factory()->create(['is_active' => true]);
    \DB::table('user_store')->insert([
        'user_id' => $user->id,
        'store_id' => $store->id,
        'is_default' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    session(['current_store_id' => $store->id]);

    $response = $this->actingAs($user)->get('/pos');
    $response->assertStatus(200);
});

test('pos checkout creates sale with stock movement', function () {
    $user = User::factory()->create();
    $store = Store::factory()->create(['is_active' => true]);
    \DB::table('user_store')->insert([
        'user_id' => $user->id,
        'store_id' => $store->id,
        'is_default' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    session(['current_store_id' => $store->id]);

    $product = Product::factory()->create([
        'store_id' => $store->id,
        'selling_price' => 10.0000,
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->post('/pos/checkout', [
        'items' => [
            ['product_id' => $product->id, 'quantity' => 2],
        ],
        'payments' => [
            ['amount' => 20, 'method' => 'cash'],
        ],
    ]);

    $response->assertRedirect(route('pos.index'));
    $this->assertDatabaseHas('sales', ['store_id' => $store->id, 'total_amount' => 20.0000]);
    $this->assertDatabaseHas('stock_movements', ['product_id' => $product->id, 'type' => 'sale']);
});
