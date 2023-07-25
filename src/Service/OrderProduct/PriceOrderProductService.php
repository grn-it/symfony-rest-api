<?php

declare(strict_types=1);

namespace App\Service\OrderProduct;

use App\Entity\OrderProduct;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(lazy: true)]
class PriceOrderProductService
{
    public function refreshPrice(OrderProduct $orderProduct): void
    {
        $orderProduct->setPrice($orderProduct->getProduct()->getPrice());
    }
}
