<?php

declare(strict_types=1);

namespace App\Service\Order\Dto\Object;

class Order
{
    public function __construct(private readonly int $id)
    {
    }
}
