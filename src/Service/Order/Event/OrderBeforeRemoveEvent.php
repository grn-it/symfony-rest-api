<?php

declare(strict_types=1);

namespace App\Service\Order\Event;

use App\Entity\Order;
use Symfony\Contracts\EventDispatcher\Event;

class OrderBeforeRemoveEvent extends Event
{
    public const NAME = 'order.before_remove';

    public function __construct(private readonly Order $order)
    {
    }

    public function getOrder(): Order
    {
        return $this->order;
    }
}
