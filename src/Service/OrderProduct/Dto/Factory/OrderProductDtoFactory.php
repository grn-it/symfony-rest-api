<?php

declare(strict_types=1);

namespace App\Service\OrderProduct\Dto\Factory;

use App\Component\Dto\Factory\DtoFactoryInterface;
use App\Component\Serializer\Serializer;
use App\Component\Validator\Validator;
use App\Entity\OrderProduct;
use App\Service\OrderProduct\Dto\DeletedOrderProductDto;
use App\Service\OrderProduct\Dto\Object\Order;
use App\Service\OrderProduct\Dto\Object\Product;
use App\Service\OrderProduct\Dto\OrderProductDto;
use InvalidArgumentException;

class OrderProductDtoFactory implements DtoFactoryInterface
{
    public function __construct(protected readonly Serializer $serializer, protected readonly Validator $validator)
    {
    }
    
    /** @param array<mixed> $context */
    public function create(object $data, string $class, array $context = []): mixed // phpcs:ignore
    {
        if (!$data instanceof OrderProduct) {
            throw new InvalidArgumentException('Object must be instance OrderProduct class.');
        }

        switch ($class) {
            case OrderProductDto::class:
                $dto = $this->createOrderProductDto($data);

                break;
            case DeletedOrderProductDto::class:
                $dto = $this->createDeletedOrderProductDto($data);

                break;
            default:
                throw new InvalidArgumentException(
                    sprintf('Class "%s" is not supported.', $class)
                );
        }

        return $dto;
    }

    private function createOrderProductDto(OrderProduct $orderProduct): OrderProductDto
    {
        return new OrderProductDto(
            $orderProduct->getId(),
            new Order($orderProduct->getOrder()->getId()),
            new Product($orderProduct->getProduct()->getId()),
            $orderProduct->getName(),
            $orderProduct->getDescription(),
            $orderProduct->getPrice(),
            $orderProduct->getQuantity()
        );
    }

    private function createDeletedOrderProductDto(OrderProduct $orderProduct): DeletedOrderProductDto
    {
        return new DeletedOrderProductDto(
            new Order($orderProduct->getOrder()->getId()),
            new Product($orderProduct->getProduct()->getId()),
            $orderProduct->getName(),
            $orderProduct->getDescription(),
            $orderProduct->getPrice(),
            $orderProduct->getQuantity()
        );
    }
}
