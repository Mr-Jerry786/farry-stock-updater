<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::insert([
            [
                'asin' => 'B00TEST001',
                'ean' => '1234567890123',
                'title' => 'Wireless Mouse',
                'stock' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'asin' => 'B00TEST002',
                'ean' => '2345678901234',
                'title' => 'Mechanical Keyboard',
                'stock' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'asin' => 'B00TEST003',
                'ean' => '3456789012345',
                'title' => 'HDMI Cable 2m',
                'stock' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
