<?php

declare(strict_types=1);

namespace App\Service\OrderProduct\Dto;

use App\Service\OrderProduct\Dto\Object\Order;
use App\Service\OrderProduct\Dto\Object\Product;

class OrderProductDto
{
    public function __construct(
        private readonly int $id,
        private readonly Order $order,
        private readonly Product $product,
        private readonly string $name,
        private readonly string $description,
        private readonly int $price,
        private readonly int $quantity
    )
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
