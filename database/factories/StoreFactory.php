<?php

namespace Database\Factories;

use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

class StoreFactory extends Factory
{
    protected $model = Store::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->lexify('STR???')),
            'name' => $this->faker->company().' Store',
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->companyEmail(),
            'address' => $this->faker->address(),
            'is_active' => true,
            'currency_code' => 'USD',
            'vat_percent' => 0,
        ];
    }
}
