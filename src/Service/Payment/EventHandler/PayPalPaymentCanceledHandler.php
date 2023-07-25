<?php

declare(strict_types=1);

namespace App\Service\Payment\EventHandler;

use App\Repository\PaymentRepository;
use App\Service\Payment\PaymentManager;

class PayPalPaymentCanceledHandler
{
    public function __construct(
        private readonly PaymentRepository $paymentRepository,
        private readonly PaymentManager $paymentManager
    )
    {
    }

    public function handle(string $uuid): void
    {
        $payment = $this->paymentRepository->get(uuid: $uuid);

        $this->paymentManager->canceled($payment);
    }
}
