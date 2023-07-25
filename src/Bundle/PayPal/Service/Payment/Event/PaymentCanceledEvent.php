<?php

declare(strict_types=1);

namespace App\Bundle\PayPal\Service\Payment\Event;

use Symfony\Contracts\EventDispatcher\Event;

class PaymentCanceledEvent extends Event
{
    public const NAME = 'payment.canceled';

    public function __construct(private readonly string $uuid)
    {
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }
}
