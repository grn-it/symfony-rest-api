<?php

declare(strict_types=1);

namespace App\Service\Order;

use App\Entity\Order;
use App\Entity\User;
use App\Repository\OrderRepository;
use App\Repository\OrderStatusRepository;
use App\Service\Order\Event\OrderCreatedEvent;
use App\Service\Order\Exception\OrderNewAlreadyExistException;
use App\Service\OrderStatus\OrderStatuses;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class OrderCreator
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly OrderStatusRepository $orderStatusRepository,
        private readonly EventDispatcherInterface $dispatcher
    )
    {
    }

    public function create(OrderStatuses $orderStatus, User $user): Order
    {
        if (OrderStatuses::isCurrent($orderStatus->value)) {
            if ($this->orderRepository->isExist(statuses: OrderStatuses::getCurrent(), user: $user->getId())) {
                throw new OrderNewAlreadyExistException('Current order already exist.');
            }
        }

        $order = new Order();
        $order->setUser($user);
        $order->setStatus($this->orderStatusRepository->get($orderStatus->value));

        $this->orderRepository->save($order);

        $this->dispatcher->dispatch(new OrderCreatedEvent($order), OrderCreatedEvent::NAME);

        return $order;
    }
}
