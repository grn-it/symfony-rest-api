<?php

declare(strict_types=1);

namespace App\Service\OrderStatus;

enum OrderStatuses: int
{
    // phpcs:disable
    case NEW                = 1;
    case PLACED             = 2;
    case PAID               = 3;
    case SENT               = 4;
    case PENDING_RECEIPT    = 5;
    case DELIVERED          = 6;
    case RETURNED           = 7;
    case CANCELED           = 8;
    // phpcs:enable

    /** @return array<int, int> */
    public static function getToPaid(): array
    {
        return [self::PLACED->value];
    }

    /**
     * @return array<int, int>
     */
    public static function getCurrent(): array
    {
        return [self::NEW->value, self::PLACED->value];
    }

    public static function isCurrent(int $orderStatus): bool
    {
        return in_array($orderStatus, self::getCurrent(), true);
    }

    /** @return array<int, string> */
    public static function getCurrentNames(): array
    {
        return [self::NEW->name, self::PLACED->name];
    }
}
