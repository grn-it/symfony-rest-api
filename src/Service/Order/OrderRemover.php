<?php

declare(strict_types=1);

namespace App\Service\Order;

use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Service\Order\Event\OrderBeforeRemoveEvent;
use App\Service\Order\Event\OrderRemovedEvent;
use App\Service\Order\Exception\OrderNotCurrentException;
use App\Service\OrderStatus\OrderStatuses;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class OrderRemover
{
    public function __construct(
        private readonly EventDispatcherInterface $dispatcher,
        private readonly OrderRepository $orderRepository
    )
    {
    }
    
    public function remove(Order $order): void
    {
        if (!OrderStatuses::isCurrent($order->getStatus()->getId())) {
            throw new OrderNotCurrentException('Only current order can be removed.');
        }

        $this->dispatcher->dispatch(new OrderBeforeRemoveEvent($order), OrderBeforeRemoveEvent::NAME);

        $this->orderRepository->remove($order);

        $this->dispatcher->dispatch(new OrderRemovedEvent($order), OrderRemovedEvent::NAME);
    }
}
