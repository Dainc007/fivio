<?php

declare(strict_types=1);

use App\Models\Product;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->foreignId('category_id')->nullable()->constrained('categories')->noActionOnDelete();
            $table->timestamps();
        });

        $productNames = [
            'Babka',
            'Popcorn',
            'Soda',
            'Skrobia',
            'Kasza',
            'Mąka',
            'Słonecznik',
            'Len',
        ];

        $products = array_map(fn ($name) => ['name' => $name], $productNames);
        Product::insert($products);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
