<?php

declare(strict_types=1);

namespace App\Service\Order;

use App\Bundle\PayPal\Client\PayPalClient;
use App\Entity\Order;
use App\Entity\Payment; // phpcs:ignore
use App\Repository\PaymentRepository;
use App\Service\Payment\Exception\PaymentAlreadyExistException;
use App\Service\Payment\PaymentCreator;
use App\Service\Payment\PaymentUpdater;
use App\Service\PaymentStatus\PaymentStatuses;
use App\Service\PaymentType\PaymentTypes;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Autoconfigure(lazy: true)]
class PayOrderService
{
    public function __construct(
        private readonly OrderManager $orderManager,
        private readonly PaymentCreator $paymentCreator,
        private readonly PaymentRepository $paymentRepository,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly PayPalClient $payPalClient,
        private readonly PaymentUpdater $paymentUpdater
    )
    {
    }

    public function pay(Order $order): PayOrder
    {
        $this->orderManager->calculateSum($order);

        try {
            $payment = $this->paymentCreator->create(
                $order,
                $order->getSum(),
                PaymentStatuses::NEW,
                PaymentTypes::TRANSFER
            );
        } catch (PaymentAlreadyExistException) {
            /** @var Payment $payment */
            $payment = $this->paymentRepository->get(
                order: $order->getId(),
                amount: $order->getSum(),
                status: PaymentStatuses::NEW->value,
                type: PaymentTypes::TRANSFER->value
            );
        }

        $sum = $order->getSum();
        $returnUrl = $this->urlGenerator->generate(
            'app_web-service_paypal_payments_complete',
            ['id' => $payment->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $description = sprintf('Payment for order #%d.', $order->getId());

        $payPalCreatedPaymentDto = $this->payPalClient->createPayment($sum, $returnUrl, $description);

        $this->paymentUpdater->updateUuid($payment, $payPalCreatedPaymentDto->getUuid());

        return new PayOrder($payPalCreatedPaymentDto->getConfirmationUrl());
    }
}
