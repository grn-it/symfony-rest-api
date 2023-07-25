<?php

declare(strict_types=1);

namespace App\Service\Order;

class PayOrder
{
    public function __construct(private readonly string $confirmationUrl)
    {
    }

    public function getConfirmationUrl(): string
    {
        return $this->confirmationUrl;
    }
}
