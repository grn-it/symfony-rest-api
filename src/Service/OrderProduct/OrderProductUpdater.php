<?php

declare(strict_types=1);

namespace App\Service\OrderProduct;

use App\Entity\OrderProduct;
use App\Repository\OrderProductRepository;
use App\Service\Order\Event\OrderUpdatedEvent;
use App\Service\Order\Exception\OrderNotCurrentException;
use App\Service\OrderStatus\OrderStatuses;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class OrderProductUpdater
{
    public function __construct(
        private readonly OrderProductRepository $orderProductRepository,
        private readonly EventDispatcherInterface $dispatcher
    )
    {
    }

    public function updateQuantity(OrderProduct $orderProduct, int $quantity): void
    {
        if ($orderProduct->getQuantity() === $quantity) {
            return;
        }

        if (!OrderStatuses::isCurrent($orderProduct->getOrder()->getStatus()->getId())) {
            throw new OrderNotCurrentException('Only product in current order can be updated.');
        }

        $orderProduct->setQuantity($quantity);

        $this->orderProductRepository->save($orderProduct);

        $this->dispatcher->dispatch(
            new OrderUpdatedEvent($orderProduct->getOrder()),
            OrderUpdatedEvent::NAME
        );
    }
}
