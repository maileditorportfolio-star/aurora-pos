<?php

use App\Models\Store;
use App\Models\User;

test('guests are redirected to the login page', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

test('authenticated users can visit the dashboard', function () {
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

    $this->actingAs($user)->get('/dashboard')->assertOk();
});
