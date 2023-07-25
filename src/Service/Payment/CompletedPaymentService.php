<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Entity\Payment;
use App\Repository\PaymentRepository;
use App\Repository\PaymentStatusRepository;
use App\Service\Payment\Event\PaymentCompletedEvent;
use App\Service\PaymentStatus\Exception\PaymentStatusNotNewException;
use App\Service\PaymentStatus\PaymentStatuses;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Autoconfigure(lazy: true)]
class CompletedPaymentService
{
    public function __construct(
        private readonly PaymentRepository $paymentRepository,
        private readonly PaymentStatusRepository $paymentStatusRepository,
        private readonly EventDispatcherInterface $dispatcher
    )
    {
    }

    public function completed(Payment $payment): void
    {
        if ($payment->getStatus()->getId() === PaymentStatuses::COMPLETED->value) {
            return;
        }

        if ($payment->getStatus()->getId() !== PaymentStatuses::NEW->value) {
            throw new PaymentStatusNotNewException(
                sprintf(
                    'The completed status can only be set for a payment with the "%s" status.',
                    PaymentStatuses::NEW->name
                )
            );
        }

        $payment->setStatus($this->paymentStatusRepository->get(PaymentStatuses::COMPLETED->value));

        $this->paymentRepository->save($payment);

        $this->dispatcher->dispatch(new PaymentCompletedEvent($payment), PaymentCompletedEvent::NAME);
    }
}
