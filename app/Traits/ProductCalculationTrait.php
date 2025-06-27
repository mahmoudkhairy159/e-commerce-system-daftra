<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Modules\Product\Models\Product;

trait ProductCalculationTrait
{


  


    public function calculateTax($price, $quantity,$tax_rate)
    {


        return ($tax_rate/100) * $price * $quantity ;
    }

    // Calculate subtotal
    public function calculateSubtotal($price, $totalTax, $quantity)
    {
        return ($price * $quantity) + $totalTax ;
    }



}