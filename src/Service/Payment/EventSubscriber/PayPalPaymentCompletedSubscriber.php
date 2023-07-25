<?php

declare(strict_types=1);

namespace App\Service\Payment\EventSubscriber;

use App\Bundle\PayPal\Service\Payment\Event\PaymentCompletedEvent as PayPalPaymentCompleted;
use App\Service\Payment\EventHandler\PayPalPaymentCompletedHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PayPalPaymentCompletedSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly PayPalPaymentCompletedHandler $payPalPaymentCompletedHandler)
    {
    }

    public function onPaymentCompleted(PayPalPaymentCompleted $event): void
    {
        $response = $this->payPalPaymentCompletedHandler->handle($event->getId());

        $event->setResponse($response);
    }

    /** @return array<string, string> */
    public static function getSubscribedEvents(): array
    {
        return [
            PayPalPaymentCompleted::NAME => 'onPayPalPaymentCompleted',
        ];
    }
}
