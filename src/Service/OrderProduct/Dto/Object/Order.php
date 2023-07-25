<?php

declare(strict_types=1);

namespace App\Service\OrderProduct\Dto\Object;

class Order
{
    public function __construct(private int $id)
    {
    }

    public function getId(): int
    {
        return $this->id;
    }
}
