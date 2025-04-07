<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Offer;
use Illuminate\Database\Seeder;

final class OfferSeeder extends Seeder
{
    public function run(): void
    {
        Offer::factory()->count(100)->create();
    }
}
