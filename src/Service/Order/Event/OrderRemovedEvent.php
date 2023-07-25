<?php

declare(strict_types=1);

namespace App\Service\Order\Event;

use App\Entity\Order;
use Symfony\Contracts\EventDispatcher\Event;

class OrderRemovedEvent extends Event
{
    public const NAME = 'order.removed';

    public function __construct(protected Order $order)
    {
    }

    public function getOrder(): Order
    {
        return $this->order;
    }
}
