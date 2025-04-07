<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Offer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

final class OfferFactory extends Factory
{
    protected $model = Offer::class;

    public function definition(): array
    {
        $maxOrderId = Order::count();

        return [
            'order_id' => rand(1, $maxOrderId),
            'user_id' => User::factory(),
            'price' => $this->faker->randomNumber(4),
            'delivery_price' => $this->faker->randomNumber(3),
            'quantity' => $this->faker->numberBetween(1, 100),
            'quantity_on_pallet' => $this->faker->numberBetween(10, 50),
            'attachment' => null, // Can be set using ->hasAttachment()
            'country_origin' => $this->faker->country(),
            'lote' => 'LOT-'.$this->faker->randomNumber(6),
            'payment_terms' => $this->faker->paragraph,
            'status' => 'pending',
        ];
    }

    public function hasAttachment()
    {
        return $this->state(function (array $attributes) {
            return [
                'attachment' => ['offer.pdf', 'specifications.docx'],
            ];
        });
    }
}
