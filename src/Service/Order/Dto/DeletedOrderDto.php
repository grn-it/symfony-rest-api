<?php

declare(strict_types=1);

namespace App\Service\Order\Dto;

use App\Service\Order\Dto\Object\OrderProduct; // phpcs:ignore
use App\Service\Order\Dto\Object\OrderStatus;
use DateTime;

class DeletedOrderDto
{
    /**
     * @param array<OrderProduct> $orderProducts
     */
    public function __construct(
        private readonly array $orderProducts,
        private readonly OrderStatus $status,
        private readonly int $sum,
        private readonly DateTime $createdAt
    )
    {
    }
}
