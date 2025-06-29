<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Support\Collection;

interface CacheServiceInterface
{
    /**
     * Get all items from cache
     */
    public function getAll(): Collection;

    /**
     * Get all active items from cache
     */
    public function getAllActive(?string $locale = null): Collection;

    /**
     * Invalidate cache
     */
    public function invalidate(): void;
}
