<?php

declare(strict_types=1);

namespace App\Service\Order\Dto\Factory;

use App\Component\Dto\Factory\DtoFactoryInterface;
use App\Entity\Order as OrderEntity;
use App\Service\Order\Dto\DeletedOrderDto;
use App\Service\Order\Dto\Object\Order;
use App\Service\Order\Dto\Object\OrderProduct;
use App\Service\Order\Dto\Object\OrderStatus;
use App\Service\Order\Dto\Object\Product;
use App\Service\Order\Dto\OrderDto;
use InvalidArgumentException;

class OrderDtoFactory implements DtoFactoryInterface
{
    /** @param array<mixed> $context */
    public function create(object $data, string $class, array $context = []): object // phpcs:ignore
    {
        if (!$data instanceof OrderEntity) {
            throw new InvalidArgumentException('Object must be instance Order class.');
        }

        switch ($class) {
            case OrderDto::class:
                $dto = $this->createOrderDto($data);

                break;
            case DeletedOrderDto::class:
                $dto = $this->createDeletedOrderDto($data);

                break;
            default:
                throw new InvalidArgumentException(
                    sprintf('Class "%s" is not supported.', $class)
                );
        }

        return $dto;
    }

    private function createOrderDto(OrderEntity $order): OrderDto
    {
        $orderProducts = [];

        foreach ($order->getOrderProducts() as $orderProduct) {
            $orderProducts[] = new OrderProduct(
                $orderProduct->getId(),
                new Order($orderProduct->getOrder()->getId()),
                $orderProduct->getName(),
                $orderProduct->getDescription(),
                $orderProduct->getPrice(),
                $orderProduct->getQuantity(),
                new Product($orderProduct->getProduct()->getId())
            );
        }

        return new OrderDto(
            $order->getId(),
            $orderProducts,
            new OrderStatus($order->getStatus()->getId()),
            $order->getSum(),
            $order->getCreatedAt()
        );
    }

    private function createDeletedOrderDto(OrderEntity $order): DeletedOrderDto
    {
        $orderProducts = [];

        foreach ($order->getOrderProducts() as $orderProduct) {
            $orderProducts[] = new OrderProduct(
                $orderProduct->getId(),
                new Order($orderProduct->getOrder()->getId()),
                $orderProduct->getName(),
                $orderProduct->getDescription(),
                $orderProduct->getPrice(),
                $orderProduct->getQuantity(),
                new Product($orderProduct->getProduct()->getId())
            );
        }

        return new DeletedOrderDto(
            $orderProducts,
            new OrderStatus($order->getStatus()->getId()),
            $order->getSum(),
            $order->getCreatedAt()
        );
    }
}
