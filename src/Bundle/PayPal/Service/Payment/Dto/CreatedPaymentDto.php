<?php

declare(strict_types=1);

namespace App\Bundle\PayPal\Service\Payment\Dto;

class CreatedPaymentDto
{
    public function __construct(
        private readonly string $uuid,
        private readonly string $status,
        private readonly string $confirmationUrl
    )
    {
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
    
    public function getConfirmationUrl(): string
    {
        return $this->confirmationUrl;
    }
}
