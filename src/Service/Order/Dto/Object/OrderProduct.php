<?php

declare(strict_types=1);

namespace App\Service\Order\Dto\Object;

class OrderProduct
{
    public function __construct(
        private readonly int $id,
        private readonly Order $order,
        private readonly string $name,
        private readonly string $description,
        private readonly int $price,
        private readonly int $quantity,
        private readonly Product $product
    )
    {
    }
}
