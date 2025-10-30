<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = Str::title($this->faker->unique()->words(3, true));
        $price = $this->faker->randomFloat(2, 19, 249);
        $salePrice = $this->faker->boolean(35) ? max($price - $this->faker->numberBetween(5, 40), 9.95) : null;

        return [
            'category_id' => Category::inRandomOrder()->value('id') ?? Category::factory(),
            'brand_id' => Brand::inRandomOrder()->value('id') ?? Brand::factory(),
            'user_id' => null,
            'name' => $name,
            'slug' => Str::slug($name . '-' . $this->faker->unique()->numberBetween(1000, 9999)),
            'sku' => strtoupper(Str::random(3)) . '-' . $this->faker->numberBetween(1000, 9999),
            'tagline' => $this->faker->sentence(8),
            'excerpt' => $this->faker->sentence(18),
            'description' => $this->faker->paragraphs(3, true),
            'price' => $price,
            'sale_price' => $salePrice,
            'stock' => $this->faker->numberBetween(10, 250),
            'min_stock' => $this->faker->numberBetween(5, 15),
            'is_featured' => $this->faker->boolean(20),
            'status' => 'published',
            'attributes' => [
                'nicotine_strength' => $this->faker->randomElement(['0mg', '3mg', '6mg', '12mg']),
                'flavour_profile' => $this->faker->randomElement(['Fruity', 'Dessert', 'Menthol', 'Classic Tobacco']),
            ],
            'metadata' => [
                'origin' => $this->faker->randomElement(['AU', 'NZ', 'US']),
                'batch' => Str::upper(Str::random(5)),
            ],
            'published_at' => now()->subDays($this->faker->numberBetween(0, 60)),
        };
    }
}
