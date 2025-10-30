<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Page>
 */
class PageFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->sentence(5);

        return [
            'title' => $title,
            'slug' => Str::slug($title . '-' . $this->faker->unique()->numberBetween(10, 9999)),
            'excerpt' => $this->faker->sentence(20),
            'body' => $this->faker->paragraphs(4, true),
            'meta' => null,
            'is_published' => true,
            'published_at' => now()->subDays($this->faker->numberBetween(0, 30)),
        ];
    }
}
