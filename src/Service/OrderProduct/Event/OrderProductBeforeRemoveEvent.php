<?php

declare(strict_types=1);

namespace App\Service\OrderProduct\Event;

use App\Entity\OrderProduct;
use Symfony\Contracts\EventDispatcher\Event;

class OrderProductBeforeRemoveEvent extends Event
{
    public const NAME = 'order-product.before_remove';

    public function __construct(protected OrderProduct $orderProduct)
    {
    }

    public function getOrderProduct(): OrderProduct
    {
        return $this->orderProduct;
    }
}
