<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MissingInventory extends Model
{
    protected $table = 'missing_inventory';

    protected $fillable = [
        'asin',
        'ean',
        'stock',
        'price',
    ];

    public $timestamps = true;

    // Optionally add casting to ensure correct data types
    protected $casts = [
        'stock' => 'integer',
        'price' => 'float',
    ];
}
