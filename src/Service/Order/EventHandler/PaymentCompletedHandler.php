<?php

declare(strict_types=1);

namespace App\Service\Order\EventHandler;

use App\Entity\Payment;
use App\Service\Order\OrderManager;

class PaymentCompletedHandler
{
    public function __construct(private readonly OrderManager $orderManager)
    {
    }

    public function handle(Payment $payment): void
    {
        $this->orderManager->paid($payment->getOrder());
    }
}
