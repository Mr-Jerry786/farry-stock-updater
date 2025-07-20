<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewProduct extends Model
{
    protected $table = 'new_products';

    protected $fillable = [
        'asin',
        'ean',
        'price',
        'discount',
        'stock',
    ];

    // If you're using Laravel timestamps (created_at/updated_at)
    public $timestamps = true;
}
