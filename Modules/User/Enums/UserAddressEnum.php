<?php

namespace Modules\User\Enums;

use Spatie\Enum\Enum;

final class UserAddressEnum extends Enum
{
        const HOME = 1;
        const WORK = 2;
        const OTHER = 3;

    public static function getConstants(): array
    {
        $reflection = new \ReflectionClass(static::class);
        return $reflection->getConstants();
    }
}