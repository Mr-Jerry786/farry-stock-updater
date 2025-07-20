<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmazonInventory extends Model
{
    protected $fillable = ['asin', 'ean', 'stock', 'price'];
}
