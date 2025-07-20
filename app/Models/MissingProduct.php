<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MissingProduct extends Model
{
    // Table name (optional if you follow Laravel naming convention)
    protected $table = 'missing_products';

    // Fillable fields for mass assignment
    protected $fillable = ['asin', 'ean', 'stock', 'price', 'discount'];

    // Enable timestamps (true by default, but keeping explicitly for clarity)
    public $timestamps = true;
}
