<?php

declare(strict_types=1);

namespace App\Bundle\PayPal\Service\Refund\Dto;

class CreatedRefundDto
{
    public function __construct(private readonly string $status)
    {
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
