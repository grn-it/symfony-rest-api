<?php

declare(strict_types=1);

namespace App\Service\OrderProduct\Event;

// phpcs:ignore
use App\Entity\OrderProduct;
use Symfony\Contracts\EventDispatcher\Event;

class OrderProductBeforeShowEvent extends Event
{
    public const NAME = 'order-product.before_show';

    /** @var array<OrderProduct> */
    private array $orderProducts;

    /** @param OrderProduct|array<OrderProduct> $data */
    public function __construct(OrderProduct|array $data)
    {
        $this->orderProducts = is_array($data) ? $data : [$data];
    }

    /** @return array<OrderProduct> */
    public function getOrderProducts(): array
    {
        return $this->orderProducts;
    }
}
