<?php

declare(strict_types=1);

namespace App\Service\OrderProduct\EventSubscriber;

use App\Service\OrderProduct\Event\OrderProductBeforeShowEvent;
use App\Service\OrderProduct\EventHandler\OrderProductBeforeShowHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderProductBeforeShowSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly OrderProductBeforeShowHandler $orderProductBeforeShowHandler)
    {
    }

    public function onOrderProductBeforeShow(OrderProductBeforeShowEvent $event): void
    {
        $this->orderProductBeforeShowHandler->handle($event->getOrderProducts());
    }

    /** @return array<string, string> */
    public static function getSubscribedEvents(): array
    {
        return [
            OrderProductBeforeShowEvent::NAME => 'onOrderProductBeforeShow',
        ];
    }
}
