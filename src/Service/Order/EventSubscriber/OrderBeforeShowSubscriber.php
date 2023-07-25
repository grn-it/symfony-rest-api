<?php

declare(strict_types=1);

namespace App\Service\Order\EventSubscriber;

use App\Service\Order\Event\OrderBeforeShowEvent;
use App\Service\Order\EventHandler\OrderBeforeShowHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderBeforeShowSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly OrderBeforeShowHandler $orderBeforeShowHandler)
    {
    }

    public function onOrderBeforeShow(OrderBeforeShowEvent $event): void
    {
        $this->orderBeforeShowHandler->handle($event->getOrders());
    }
    
    /** @return array<string, string> */
    public static function getSubscribedEvents(): array
    {
        return [
            OrderBeforeShowEvent::NAME => 'onOrderBeforeShow'
        ];
    }
}
