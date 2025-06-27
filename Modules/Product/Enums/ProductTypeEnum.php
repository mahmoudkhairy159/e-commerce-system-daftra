<?php

namespace Modules\Product\Enums;

use Spatie\Enum\Enum;

final class ProductTypeEnum extends Enum
{
    const NEW_ARRIVAL = 0;
    const  FEATURED= 1;
    const TOP_PRODUCT = 2;
    const BEST_SELLER = 3;


    public static function getConstants(): array
    {
        $reflection = new \ReflectionClass(static::class);
        return $reflection->getConstants();
    }
}
