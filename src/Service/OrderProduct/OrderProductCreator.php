<?php

declare(strict_types=1);

namespace App\Service\OrderProduct;

use App\Entity\OrderProduct;
use App\Entity\User;
use App\Repository\OrderProductRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Service\Order\Exception\OrderNotBelongToUserException;
use App\Service\Order\Exception\OrderNotCurrentException;
use App\Service\OrderProduct\Event\OrderProductCreatedEvent;
use App\Service\OrderStatus\OrderStatuses;
use App\Service\Product\Exception\ProductAlreadyInOrderException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class OrderProductCreator
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly ProductRepository $productRepository,
        private readonly OrderProductRepository $orderProductRepository,
        private readonly EventDispatcherInterface $dispatcher
    )
    {
    }
    
    public function create(int $order, int $product, int $quantity, User $user): OrderProduct
    {
        $order = $this->orderRepository->get($order);
        $product = $this->productRepository->get($product);

        if ($order->getUser() !== $user) {
            throw new OrderNotBelongToUserException(
                sprintf('Order not belong to user "%d"', $user->getId())
            );
        }
        
        if (!OrderStatuses::isCurrent($order->getStatus()->getId())) {
            throw new OrderNotCurrentException('Can add item to current order only.');
        }
        
        // phpcs:ignore
        if ($this->orderProductRepository->isExist(
            order: $order->getId(),
            product: $product->getId(),
            user: $user->getId()
        )) {
            throw new ProductAlreadyInOrderException(
                sprintf('Product "%d" already in order "%d".', $product->getId(), $order->getId())
            );
        }

        $orderProduct = new OrderProduct();

        $orderProduct->setName($product->getName());
        $orderProduct->setDescription($product->getDescription());
        $orderProduct->setOrder($order);
        $orderProduct->setQuantity($quantity);
        $orderProduct->setProduct($product);

        $this->orderProductRepository->save($orderProduct);

        $this->dispatcher->dispatch(
            new OrderProductCreatedEvent($orderProduct),
            OrderProductCreatedEvent::NAME
        );

        return $orderProduct;
    }
}
