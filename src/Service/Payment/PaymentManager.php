<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Entity\Payment;

class PaymentManager
{
    public function __construct(
        private readonly CompletedPaymentService $completedPaymentService,
        private readonly CanceledPaymentService $canceledPaymentService
    )
    {
    }

    public function completed(Payment $payment): void
    {
        $this->completedPaymentService->completed($payment);
    }

    public function canceled(Payment $payment): void
    {
        $this->canceledPaymentService->canceled($payment);
    }
}
