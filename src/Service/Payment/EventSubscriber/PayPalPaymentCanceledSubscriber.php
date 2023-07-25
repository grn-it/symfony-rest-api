<?php

declare(strict_types=1);

namespace App\Service\Payment\EventSubscriber;

use App\Bundle\PayPal\Service\Payment\Event\PaymentCanceledEvent as PayPalPaymentCanceled;
use App\Service\Payment\EventHandler\PayPalPaymentCanceledHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PayPalPaymentCanceledSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly PayPalPaymentCanceledHandler $payPalPaymentCanceledHandler)
    {
    }

    public function onPaymentCanceled(PayPalPaymentCanceled $event): void
    {
        $this->payPalPaymentCanceledHandler->handle($event->getUuid());
    }

    /** @return array<string, string> */
    public static function getSubscribedEvents(): array
    {
        return [
            PayPalPaymentCanceled::NAME => 'onPayPalPaymentCanceled',
        ];
    }
}
