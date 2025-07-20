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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->nullable()->after('ean');
            $table->decimal('discount', 8, 2)->nullable()->after('price');
        });

        Schema::table('missing_products', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->nullable()->after('ean');
            $table->decimal('discount', 8, 2)->nullable()->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['price', 'discount']);
        });

        Schema::table('missing_products', function (Blueprint $table) {
            $table->dropColumn(['price', 'discount']);
        });
    }
};
