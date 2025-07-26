<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use App\OrderStatuses;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::customer()->inRandomOrder()->first()?->id ?? User::factory()->customer()->create()->id,
            'status' => OrderStatuses::Pending->label(),
            'total_price' => 0, // Will be calculated after creating items
        ];
    }

    /**
     * Create an order with random order items (1-5 items)
     */
    public function withItems(): static
    {
        return $this->afterCreating(function ($order) {
            $this->createOrderItems($order, $this->faker->numberBetween(1, 5));
        });
    }

    /**
     * Helper method to create order items
     */
    private function createOrderItems($order, int $itemCount): void
    {
        $totalPrice = 0;

        // Get random products for the order items
        $products = Product::inRandomOrder()->limit($itemCount)->get();

        // Loop through the products and create order items
        foreach ($products as $product) {
            $quantity = $this->faker->numberBetween(1, 3);

            // check if the product has enough stock
            if (! $product->isInStock($quantity)) {
                $quantity = $product->stock_quantity - 1;
            }

            // create order items
            $orderItem = $order->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $product->price,
            ]);

            // decrease stock quantity
            $product->decreaseStockQuantity($quantity);

            $totalPrice += $orderItem->unit_price * $orderItem->quantity;
        }

        // Update the order's total price
        $order->update(['total_price' => $totalPrice]);
    }
}
