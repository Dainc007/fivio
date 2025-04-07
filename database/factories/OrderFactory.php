<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

final class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $maxProductId = Product::count();
        $statuses = OrderStatus::cases();

        return [
            'product_id' => rand(1, $maxProductId),
            'address_id' => null, // Can be set using ->has(Address::factory())
            'quantity' => $this->faker->numberBetween(1, 100),
            'price' => $this->faker->randomNumber(4),
            'delivery_date' => $this->faker->dateTimeBetween('+1 week', '+2 months'),
            'attachment' => null, // Can be set using ->hasAttachment()
            'status' => ($this->faker->randomElement($statuses))->value,
        ];
    }

    public function hasAddress()
    {
        return $this->state(function (array $attributes) {
            return [
                'address_id' => \App\Models\Address::factory(),
            ];
        });
    }

    public function hasAttachment()
    {
        return $this->state(function (array $attributes) {
            return [
                'attachment' => ['file1.pdf', 'file2.png'],
            ];
        });
    }
}
