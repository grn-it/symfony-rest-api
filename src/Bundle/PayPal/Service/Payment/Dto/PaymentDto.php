<?php

declare(strict_types=1);

namespace App\Bundle\PayPal\Service\Payment\Dto;

use DateTime;

class PaymentDto
{
    public function __construct(
        private readonly int $amount,
        private readonly string $uuid,
        private readonly string $status,
        private readonly string $description,
        private readonly string $type,
        private readonly string $returnUrl,
        private readonly DateTime $createdAt
    )
    {
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getType(): string
    {
        return $this->getType();
    }

    public function getReturnUrl(): string
    {
        return $this->returnUrl;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }
}
