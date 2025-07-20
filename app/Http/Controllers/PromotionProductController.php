<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PromotionProduct;

class PromotionProductController extends Controller
{
    public function index()
    {
        $promotions = PromotionProduct::latest()->paginate(15);
        return view('promotions.index', compact('promotions'));
    }
}
