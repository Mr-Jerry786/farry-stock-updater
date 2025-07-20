<?php

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
        Schema::create('missing_products', function (Blueprint $table) {
            $table->id();
            $table->string('asin')->nullable();
            $table->string('ean')->nullable();
            $table->integer('stock')->default(0);
            $table->timestamps();

            $table->unique(['asin', 'ean']); // Prevent duplicate ASIN/EAN combos
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('missing_products');
    }
};
