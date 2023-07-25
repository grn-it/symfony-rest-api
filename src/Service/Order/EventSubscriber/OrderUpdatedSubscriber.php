<?php

declare(strict_types=1);

namespace App\Service\Order\EventSubscriber;

use App\Service\Order\Event\OrderUpdatedEvent;
use App\Service\Order\EventHandler\OrderUpdatedHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderUpdatedSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly OrderUpdatedHandler $orderUpdatedHandler)
    {
    }

    public function onOrderUpdated(OrderUpdatedEvent $event): void
    {
        $this->orderUpdatedHandler->handle($event->getOrder());
    }

    /** @return array<string, string> */
    public static function getSubscribedEvents(): array
    {
        return [
            OrderUpdatedEvent::NAME => 'onOrderUpdated',
        ];
    }
}
