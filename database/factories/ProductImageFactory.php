<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductImage>
 */
class ProductImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'path' => 'https://picsum.photos/seed/' . $this->faker->uuid() . '/800/1000',
            'alt_text' => $this->faker->sentence(4),
            'position' => 0,
            'is_primary' => true,
        ];
    }
}
