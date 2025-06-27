<?php

namespace Modules\Order\Enums;

use Spatie\Enum\Enum;

final class OrderPaymentMethodEnum extends Enum
{
  // Enum values for payment_method
  const CASH = 0;
  const CREDIT_CARD = 1;
  const PAYPAL =2;
  const BANK_TRANSFER = 3;
  // Enum values for payment_method


    public static function getConstants(): array
    {
        $reflection = new \ReflectionClass(static::class);
        return $reflection->getConstants();
    }
}
