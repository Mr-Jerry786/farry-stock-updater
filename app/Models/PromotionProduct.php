<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionProduct extends Model
{
    protected $fillable = [
        'asin',
        'ean',
        'title',
        'price',
        'discount',
        'stock',
        'promotion_end_date',
    ];
}
