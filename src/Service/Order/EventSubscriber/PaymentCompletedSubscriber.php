<?php

declare(strict_types=1);

namespace App\Service\Order\EventSubscriber;

use App\Service\Order\EventHandler\PaymentCompletedHandler;
use App\Service\Payment\Event\PaymentCompletedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PaymentCompletedSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly PaymentCompletedHandler $paymentCompletedHandler)
    {
    }

    public function onPaymentCompleted(PaymentCompletedEvent $event): void
    {
        $this->paymentCompletedHandler->handle($event->getPayment());
    }

    /** @return array<string, string> */
    public static function getSubscribedEvents(): array
    {
        return [
            PaymentCompletedEvent::NAME => 'onPaymentCompleted',
        ];
    }
}
