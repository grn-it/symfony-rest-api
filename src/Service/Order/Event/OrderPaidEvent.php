<?php

declare(strict_types=1);

namespace App\Service\Order\Event;

use App\Component\EventDispatcher\Event\AbstractEvent;
use App\Entity\Order;

class OrderPaidEvent extends AbstractEvent
{
    public const NAME = 'order.paid';

    public function __construct(protected Order $order)
    {
    }

    public function getOrder(): Order
    {
        return $this->order;
    }
}
