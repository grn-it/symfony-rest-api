<?php

declare(strict_types=1);

namespace App\Service\OrderProduct;

use App\Entity\OrderProduct;

class OrderProductManager
{
    public function __construct(private readonly PriceOrderProductService $priceOrderProductService)
    {
    }

    public function refreshPrice(OrderProduct $orderProduct): void
    {
        $this->priceOrderProductService->refreshPrice($orderProduct);
    }
}
