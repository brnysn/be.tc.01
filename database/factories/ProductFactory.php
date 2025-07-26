<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => ucwords($this->faker->word()),
            'description' => $this->faker->sentence(),
            'sku' => $this->faker->unique()->numerify('SKU####'),
            'price' => $this->faker->randomFloat(2, 10, 100),
            'stock_quantity' => $this->faker->numberBetween(1, 100),
        ];
    }
}
