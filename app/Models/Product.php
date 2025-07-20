<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // Allow mass assignment for these fields
    protected $fillable = ['asin', 'ean', 'stock', 'price', 'discount'];

    // Enable timestamps (created_at, updated_at)
    public $timestamps = true;
}
