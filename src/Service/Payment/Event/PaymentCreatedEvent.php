<?php

declare(strict_types=1);

namespace App\Service\Payment\Event;

use App\Entity\Payment;
use Symfony\Contracts\EventDispatcher\Event;

class PaymentCreatedEvent extends Event
{
    public const NAME = 'payment.created';

    public function __construct(protected Payment $payment)
    {
    }

    public function getPayment(): Payment
    {
        return $this->payment;
    }
}
