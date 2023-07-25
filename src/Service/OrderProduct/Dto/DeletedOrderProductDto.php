<?php

declare(strict_types=1);

namespace App\Service\OrderProduct\Dto;

use App\Service\OrderProduct\Dto\Object\Order;
use App\Service\OrderProduct\Dto\Object\Product;

class DeletedOrderProductDto
{
    public function __construct(
        private readonly Order $order,
        private readonly Product $product,
        private readonly string $name,
        private readonly string $description,
        private readonly int $price,
        private readonly int $quantity
    )
    {
    }
}
