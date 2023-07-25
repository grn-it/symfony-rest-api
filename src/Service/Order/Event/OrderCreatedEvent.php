<?php

declare(strict_types=1);

namespace App\Service\Order\Event;

use App\Component\EventDispatcher\Event\AbstractEvent;
use App\Entity\Order;

class OrderCreatedEvent extends AbstractEvent
{
    public const NAME = 'order.created';

    public function __construct(protected Order $order)
    {
    }

    public function getOrder(): Order
    {
        return $this->order;
    }
}
