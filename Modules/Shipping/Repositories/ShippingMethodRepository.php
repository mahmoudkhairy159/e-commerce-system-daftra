<?php

namespace Modules\Shipping\Repositories;

use Modules\Shipping\Enums\ShippingMethodType;
use Modules\Shipping\Models\ShippingMethod;
use Prettus\Repository\Eloquent\BaseRepository;

class ShippingMethodRepository extends BaseRepository
{

    public function model(){
        return ShippingMethod::class;
    }
    public function calculateShippingMethodAmount(ShippingMethod $shippingMethod,float $distance = 0): float
    {
        // Ensure distance is not negative
        $distance = max(0, $distance);

        switch ($shippingMethod->type) {
            case ShippingMethodType::LOCAL_STATE:
                // For local state, just use the flat rate
                return $shippingMethod->flat_rate;

            case ShippingMethodType::EXTERNAL_STATE:
                // For external state, calculate based on distance
                if ($distance <= 0) {
                    return $shippingMethod->flat_rate;
                }

                // If distance exceeds max_distance and max_distance is set, cap it
                if ($shippingMethod->max_distance > 0 && $distance > $shippingMethod->max_distance) {
                    $distance = $shippingMethod->max_distance;
                }

                return $shippingMethod->flat_rate + ($distance * $shippingMethod->per_km_rate);

            case ShippingMethodType::HYBRID:
                // For hybrid, combine both approaches
                if ($distance <= 0) {
                    return $shippingMethod->flat_rate;
                }

                // If distance exceeds max_distance and max_distance is set, cap it
                if ($shippingMethod->max_distance > 0 && $distance > $shippingMethod->max_distance) {
                    $distance = $shippingMethod->max_distance;
                }

                return $shippingMethod->flat_rate + ($distance * $shippingMethod->per_km_rate);

            default:
                // Default to flat rate if type is not recognized
                return $shippingMethod->flat_rate;
        }
    }

}
