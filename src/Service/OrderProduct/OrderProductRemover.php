<?php

declare(strict_types=1);

namespace App\Service\OrderProduct;

use App\Entity\OrderProduct;
use App\Repository\OrderProductRepository;
use App\Service\Order\Exception\OrderNotCurrentException;
use App\Service\OrderProduct\Event\OrderProductBeforeRemoveEvent;
use App\Service\OrderProduct\Event\OrderProductRemovedEvent;
use App\Service\OrderStatus\OrderStatuses;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class OrderProductRemover
{
    public function __construct(
        private readonly EventDispatcherInterface $dispatcher,
        private readonly OrderProductRepository $orderProductRepository
    )
    {
    }

    public function remove(OrderProduct $orderProduct): void
    {
        if (!OrderStatuses::isCurrent($orderProduct->getOrder()->getStatus()->getId())) {
            throw new OrderNotCurrentException('Only product in current order can be removed.');
        }

        $this->dispatcher->dispatch(
            new OrderProductBeforeRemoveEvent($orderProduct),
            OrderProductBeforeRemoveEvent::NAME
        );

        $this->orderProductRepository->remove($orderProduct);

        $this->dispatcher->dispatch(
            new OrderProductRemovedEvent($orderProduct),
            OrderProductRemovedEvent::NAME
        );
    }
}
