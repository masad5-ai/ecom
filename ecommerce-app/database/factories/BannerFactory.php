<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Banner>
 */
class BannerFactory extends Factory
{
    public function definition(): array
    {
        $title = Str::title($this->faker->words(3, true));

        return [
            'title' => $title,
            'subtitle' => $this->faker->sentence(12),
            'image' => 'https://picsum.photos/seed/' . $this->faker->uuid() . '/1200/600',
            'mobile_image' => null,
            'button_label' => 'Shop now',
            'button_url' => '#',
            'is_active' => true,
            'position' => $this->faker->numberBetween(0, 10),
            'meta' => null,
        ];
    }
}
