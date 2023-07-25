<?php

declare(strict_types=1);

namespace App\Bundle\PayPal\Service\Payment\Dto;

class CreatePaymentDto
{
    public function __construct(
        private readonly int $amount,
        private readonly string $returnUrl,
        private readonly string $description
    )
    {
    }
}
