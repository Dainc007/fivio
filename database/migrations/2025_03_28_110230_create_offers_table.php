<?php

declare(strict_types=1);

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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->noActionOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('price');
            $table->string('delivery_price');
            $table->integer('quantity');
            $table->integer('quantity_on_pallet');
            $table->json('attachment')->nullable();
            $table->string('country_origin')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('lote')->nullable();
            $table->text('payment_terms')->nullable();
            $table->text('comment')->nullable();
            $table->string('status')->default('pending');

            $table->unique(['order_id', 'user_id', 'status']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
