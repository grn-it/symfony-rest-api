<?php

declare(strict_types=1);

namespace App\Service\OrderProduct\Dto\Object;

class Product
{
    public function __construct(private readonly int $id)
    {
    }

    public function getId(): int
    {
        return $this->id;
    }
}
