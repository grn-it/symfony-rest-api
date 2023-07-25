<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Component\Exception\EntityPropertyNotSetException;
use App\Entity\Payment;
use App\Repository\PaymentRepository;

class PaymentUpdater
{
    public function __construct(private readonly PaymentRepository $paymentRepository)
    {
    }

    public function updateUuid(Payment $payment, string $uuid): void
    {
        try {
            if ($payment->getUuid() === $uuid) {
                return;
            }
        } catch (EntityPropertyNotSetException) {
        }

        $payment->setUuid($uuid);

        $this->paymentRepository->save($payment);
    }
}
