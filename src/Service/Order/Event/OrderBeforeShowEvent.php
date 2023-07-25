<?php

declare(strict_types=1);

namespace App\Service\Order\Event;

use App\Entity\Order;
use Symfony\Contracts\EventDispatcher\Event;

class OrderBeforeShowEvent extends Event
{
    public const NAME = 'order.before_show';
    
    /** @var array<Order> */
    private array $orders;

    /**
     * @param Order|array<Order> $orders
     */
    public function __construct(Order|array $orders)
    {
        $this->orders = is_array($orders) ? $orders : [$orders];
    }

    /** @return array<Order> */
    public function getOrders(): array
    {
        return $this->orders;
    }
}
