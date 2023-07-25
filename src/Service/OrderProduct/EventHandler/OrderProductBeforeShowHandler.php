<?php

declare(strict_types=1);

namespace App\Service\OrderProduct\EventHandler;

use App\Entity\OrderProduct; // phpcs:ignore
use App\Service\OrderProduct\OrderProductManager;

class OrderProductBeforeShowHandler
{
    public function __construct(private readonly OrderProductManager $orderProductManager)
    {
    }

    /** @param array<OrderProduct> $orderProducts */
    public function handle(array $orderProducts): void
    {
        foreach ($orderProducts as $orderProduct) {
            $this->orderProductManager->refreshPrice($orderProduct);
        }
    }
}
