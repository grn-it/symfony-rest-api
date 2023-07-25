<?php

declare(strict_types=1);

namespace App\Service\Order\EventHandler;

// phpcs:ignore
use App\Entity\Order;
use App\Service\Order\OrderManager;
use App\Service\OrderProduct\OrderProductManager;
use App\Service\OrderStatus\OrderStatuses;

class OrderBeforeShowHandler
{
    public function __construct(
        private readonly OrderManager $orderManager,
        private readonly OrderProductManager $orderProductManager
    )
    {
    }

    /** @param array<Order> $orders */
    public function handle(array $orders): void
    {
        foreach ($orders as $order) {
            switch ($order->getStatus()->getId()) {
                case OrderStatuses::NEW->value:
                case OrderStatuses::PLACED->value:
                    foreach ($order->getOrderProducts() as $orderProduct) {
                        $this->orderProductManager->refreshPrice($orderProduct);
                    }
    
                    $this->orderManager->calculateSum($order);
                    break;
            }
        }
    }
}
