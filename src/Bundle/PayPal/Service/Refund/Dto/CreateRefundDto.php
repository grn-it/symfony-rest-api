<?php

declare(strict_types=1);

namespace App\Bundle\PayPal\Service\Refund\Dto;

class CreateRefundDto
{
    public function __construct(private readonly string $uuid, private readonly string $description)
    {
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
