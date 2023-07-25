<?php

declare(strict_types=1);

namespace App\Service\OrderProduct\Dto;

use App\Service\OrderProduct\Dto\Object\Order;
use App\Service\OrderProduct\Dto\Object\Product;
use Symfony\Component\Validator\Constraints as Assert;

class CreateOrderProductDto
{
    public function __construct(
        private Order $order,
        private Product $product,
        #[Assert\Positive]
        private int $quantity
    )
    {
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
