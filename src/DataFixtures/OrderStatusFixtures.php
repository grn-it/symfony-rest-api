<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\OrderStatus;
use App\Service\OrderStatus\OrderStatuses;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OrderStatusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $orderStatus = new OrderStatus();
        $orderStatus->setName(strtolower(OrderStatuses::NEW->name));
        $manager->persist($orderStatus);

        $orderStatus = new OrderStatus();
        $orderStatus->setName(strtolower(OrderStatuses::PLACED->name));
        $manager->persist($orderStatus);

        $orderStatus = new OrderStatus();
        $orderStatus->setName(strtolower(OrderStatuses::PAID->name));
        $manager->persist($orderStatus);

        $orderStatus = new OrderStatus();
        $orderStatus->setName(strtolower(OrderStatuses::SENT->name));
        $manager->persist($orderStatus);

        $orderStatus = new OrderStatus();
        $orderStatus->setName(strtolower(OrderStatuses::PENDING_RECEIPT->name));
        $manager->persist($orderStatus);

        $orderStatus = new OrderStatus();
        $orderStatus->setName(strtolower(OrderStatuses::DELIVERED->name));
        $manager->persist($orderStatus);

        $orderStatus = new OrderStatus();
        $orderStatus->setName(strtolower(OrderStatuses::RETURNED->name));
        $manager->persist($orderStatus);

        $orderStatus = new OrderStatus();
        $orderStatus->setName(strtolower(OrderStatuses::CANCELED->name));
        $manager->persist($orderStatus);

        $manager->flush();
    }
}
