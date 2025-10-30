<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_number' => 'ORD-' . Str::upper(Str::random(8)),
            'user_id' => User::factory(),
            'status' => $this->faker->randomElement(['pending', 'processing', 'completed']),
            'payment_status' => $this->faker->randomElement(['pending', 'paid']),
            'payment_method' => $this->faker->randomElement(['card', 'afterpay', 'paypal']),
            'billing_address_id' => Address::factory(),
            'shipping_address_id' => Address::factory(),
            'subtotal' => $this->faker->randomFloat(2, 49, 299),
            'discount_total' => 0,
            'tax_total' => $this->faker->randomFloat(2, 4, 45),
            'shipping_total' => $this->faker->randomFloat(2, 0, 15),
            'grand_total' => $this->faker->randomFloat(2, 59, 350),
            'currency' => 'AUD',
            'notes' => $this->faker->boolean(20) ? $this->faker->sentence(8) : null,
            'placed_at' => now()->subDays($this->faker->numberBetween(0, 20)),
        ];
    }
}
