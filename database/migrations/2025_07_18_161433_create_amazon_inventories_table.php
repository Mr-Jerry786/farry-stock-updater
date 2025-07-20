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
        Schema::create('amazon_inventories', function (Blueprint $table) {
            $table->id();
            $table->string('asin')->nullable()->index();
            $table->string('ean')->nullable()->index();
            $table->integer('stock')->default(0);
            $table->decimal('price', 8, 2)->nullable();
            $table->timestamps();

            // Prevent duplicates on same ASIN + EAN combo
            $table->unique(['asin', 'ean']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amazon_inventories');
    }
};
