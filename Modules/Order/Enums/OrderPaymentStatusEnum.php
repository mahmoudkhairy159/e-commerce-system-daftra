<?php

namespace Modules\Order\Enums;

use Spatie\Enum\Enum;

final class OrderPaymentStatusEnum extends Enum
{
  // Enum values for payment_status
  const PENDING = 0;
  const PAID = 1;
  const FAILED =2;
  const REFUNDED = 3;
  // Enum values for payment_status


    public static function getConstants(): array
    {
        $reflection = new \ReflectionClass(static::class);
        return $reflection->getConstants();
    }
}