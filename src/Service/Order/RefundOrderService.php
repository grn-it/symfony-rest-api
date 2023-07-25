<?php

declare(strict_types=1);

namespace App\Service\Order;

use App\Bundle\PayPal\Client\PayPalClient;
use App\Entity\Order;
use App\Repository\PaymentRepository;
use App\Service\Payment\PaymentCreator;
use App\Service\PaymentStatus\PaymentStatuses;
use App\Service\PaymentType\PaymentTypes;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(lazy: true)]
class RefundOrderService
{
    public function __construct(
        private readonly PaymentRepository $paymentRepository,
        private readonly PaymentCreator $paymentCreator,
        private readonly PayPalClient $payPalClient
    )
    {
    }

    public function refund(Order $order): void
    {
        $payment = $this->paymentRepository->get(
            order: $order->getId(),
            status: PaymentStatuses::COMPLETED->value,
            type: PaymentTypes::TRANSFER->value,
            amount: $order->getSum()
        );
        
        $description = sprintf('Refund for order #%d.', $order->getId());

        $this->payPalClient->createRefund($payment->getUuid(), $description);

        $this->paymentCreator->create(
            order: $order,
            amount: $order->getSum(),
            status: PaymentStatuses::COMPLETED,
            type: PaymentTypes::WITHDRAW
        );
    }
}
