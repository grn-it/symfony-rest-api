<?php

declare(strict_types=1);

namespace App\Service\Order\Dto\Object;

class OrderStatus
{
    public function __construct(private readonly int $id)
    {
    }
}
