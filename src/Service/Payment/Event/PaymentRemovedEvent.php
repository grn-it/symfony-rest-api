<?php

declare(strict_types=1);

namespace App\Service\Payment\Event;

use App\Component\EventDispatcher\Event\AbstractEvent;
use App\Entity\Payment;

class PaymentRemovedEvent extends AbstractEvent
{
    public const NAME = 'payment.removed';

    public function __construct(private readonly Payment $payment)
    {
    }

    public function getPayment(): Payment
    {
        return $this->payment;
    }
}
