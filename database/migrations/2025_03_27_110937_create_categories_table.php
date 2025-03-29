<?php

declare(strict_types=1);

use App\Models\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        $categoryNames = [
            'Superfoods',
            'Słodkie przekąski',
            'Suszone owoce',
            'Ryż',
            'Rośliny strączkowe',
            'Przyprawy',
            'Pestki, nasiona, ziarna',
            'Orzechy',
            'Mąki',
            'Kasze',
            'Do pieczenia',
            'Cukry i słodziki',
            'Płatki',
            'Kawy',
        ];

        $categories = array_map(fn ($name) => ['name' => $name], $categoryNames);
        Category::insert($categories);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
