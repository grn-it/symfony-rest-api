<?php

declare(strict_types=1);

namespace App\Service\Order;

use App\Entity\Order;
use App\Service\OrderProduct\OrderProductManager;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(lazy: true)]
class SumOrderService
{
    public function __construct(private readonly OrderProductManager $orderProductManager)
    {
    }

    public function calculateSum(Order $order): void
    {
        $sum = 0;

        foreach ($order->getOrderProducts() as $orderProduct) {
            $sum += $orderProduct->getProduct()->getPrice() * $orderProduct->getQuantity();
        }

        $order->setSum($sum);
    }
}
