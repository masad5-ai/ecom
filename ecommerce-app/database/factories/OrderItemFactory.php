<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        $product = Product::factory()->create();
        $quantity = $this->faker->numberBetween(1, 4);
        $unitPrice = $product->activePrice();

        return [
            'order_id' => Order::factory(),
            'product_id' => $product->id,
            'product_name' => $product->name,
            'sku' => $product->sku,
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'discount_total' => 0,
            'tax_total' => round($unitPrice * $quantity * 0.1, 2),
            'line_total' => $unitPrice * $quantity,
            'meta' => null,
        ];
    }
}
