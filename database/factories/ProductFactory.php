<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
final class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'category_id' => null, // Can be set using ->hasCategory()
        ];
    }

    public function hasCategory()
    {
        return $this->state(function (array $attributes) {
            return [
                'category_id' => \App\Models\Category::all()->random()->id,
            ];
        });
    }
}
