<?php

declare(strict_types=1);

namespace App\Service\Order;

use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Repository\OrderStatusRepository;
use App\Service\Order\Event\OrderPaidEvent;
use App\Service\OrderProduct\OrderProductManager;
use App\Service\OrderStatus\OrderStatuses;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Autoconfigure(lazy: true)]
class PaidOrderService
{
    public function __construct(
        private readonly OrderManager $orderManager,
        private readonly OrderRepository $orderRepository,
        private readonly OrderProductManager $orderProductManager,
        private readonly OrderStatusRepository $orderStatusRepository,
        private readonly EventDispatcherInterface $dispatcher
    )
    {
    }

    public function paid(Order $order): void
    {
        if ($order->getStatus()->getId() === OrderStatuses::PAID->value) {
            return;
        }

        foreach ($order->getOrderProducts() as $orderProduct) {
            $this->orderProductManager->refreshPrice($orderProduct);
        }
        
        $this->orderManager->calculateSum($order);
        
        $order->setStatus($this->orderStatusRepository->get(OrderStatuses::PAID->value));
        
        $this->orderRepository->save($order);

        $this->dispatcher->dispatch(new OrderPaidEvent($order), OrderPaidEvent::NAME);
    }
}
