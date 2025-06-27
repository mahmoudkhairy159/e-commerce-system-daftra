<?php

namespace Modules\Order\Enums;

use Spatie\Enum\Enum;

final class OrderStatusEnum extends Enum
{
    // Enum values for order_status
    const PENDING = 0;
    const PROCESSING = 1;
    const SHIPPED = 2;
    const DELIVERED = 3;
    const CANCELLED = 4;
    const REFUNDED = 5;
    const RETURNED = 6;
    // Enum values for order_status



    public static function getConstants(): array
    {
        $reflection = new \ReflectionClass(static::class);
        return $reflection->getConstants();
    }

    /**
     * Get display name for the status
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PROCESSING => 'Processing',
            self::SHIPPED => 'Shipped',
            self::DELIVERED => 'Delivered',
            self::CANCELLED => 'Cancelled',
            self::RETURNED => 'Returned',
            self::REFUNDED => 'Refunded',
        };
    }

    /**
     * Get color code for the status (useful for UI)
     *
     * @return string
     */
    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'gray',
            self::PROCESSING => 'blue',
            self::SHIPPED => 'orange',
            self::DELIVERED => 'green',
            self::CANCELLED => 'red',
            self::RETURNED => 'purple',
            self::REFUNDED => 'yellow',
        };
    }

    /**
     * Get valid transitions from current status
     *
     * @return array
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::PENDING => [self::PROCESSING, self::CANCELLED],
            self::PROCESSING => [self::SHIPPED, self::CANCELLED],
            self::SHIPPED => [self::DELIVERED, self::RETURNED],
            self::DELIVERED => [self::RETURNED],
            self::RETURNED => [self::REFUNDED, self::PROCESSING],
            self::CANCELLED => [],  // Terminal state
            self::REFUNDED => [],   // Terminal state
        };
    }

    /**
     * Check if transition to new status is valid
     *
     * @param OrderStatusEnum $newStatus
     * @return bool
     */
    public function canTransitionTo(OrderStatusEnum $newStatus): bool
    {
        return in_array($newStatus, $this->allowedTransitions());
    }

    /**
     * Get available statuses for admin (who can override normal flow)
     *
     * @return array
     */
    public static function adminAvailableStatuses(): array
    {
        return self::cases();
    }

    /**
     * Check if status is considered final
     *
     * @return bool
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::DELIVERED, self::CANCELLED, self::REFUNDED]);
    }

    /**
     * Check if status represents an active order
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return in_array($this, [self::PENDING, self::PROCESSING, self::SHIPPED]);
    }

    /**
     * Check if status is considered successful
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this === self::DELIVERED;
    }

    /**
     * Get status description for notifications
     *
     * @return string
     */
    public function description(): string
    {
        return match ($this) {
            self::PENDING => 'Your order has been received and is awaiting processing.',
            self::PROCESSING => 'Your order is being prepared by the vendor.',
            self::SHIPPED => 'Your order has been shipped and is on its way to you.',
            self::DELIVERED => 'Your order has been delivered successfully.',
            self::CANCELLED => 'Your order has been cancelled.',
            self::RETURNED => 'Your order has been returned.',
            self::REFUNDED => 'A refund has been processed for your order.',
        };
    }
}
