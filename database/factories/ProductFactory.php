<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);

        return [
            'store_id' => Store::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name).'-'.strtolower(Str::random(6)),
            'sku' => strtoupper(Str::random(10)),
            'purchase_price' => 5.0000,
            'selling_price' => 10.0000,
            'wholesale_price' => 8.0000,
            'min_stock_alert' => 5,
            'track_inventory' => true,
            'is_active' => true,
            'has_variants' => false,
            'tax_rate' => 0,
        ];
    }
}
