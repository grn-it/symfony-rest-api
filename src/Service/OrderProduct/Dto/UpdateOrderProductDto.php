<?php

declare(strict_types=1);

namespace App\Service\OrderProduct\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateOrderProductDto
{
    public function __construct(#[Assert\Positive] private readonly int $quantity)
    {
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
