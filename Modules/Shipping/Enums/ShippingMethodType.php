<?php

namespace Modules\Shipping\Enums;

use Spatie\Enum\Enum;

final class ShippingMethodType extends Enum
{
    const  LOCAL_STATE = 'local_state';
    const  EXTERNAL_STATE = 'external_state';
    const  HYBRID = 'hybrid';


    public static function getConstants(): array
    {
        $reflection = new \ReflectionClass(static::class);
        return $reflection->getConstants();
    }
}
