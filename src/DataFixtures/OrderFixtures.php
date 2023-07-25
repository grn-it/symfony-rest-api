<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Order;
use App\Repository\OrderStatusRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly OrderStatusRepository $orderStatusRepository
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $order = new Order();
        $order->setUser($this->userRepository->get(1));
        $order->setStatus($this->orderStatusRepository->get(2));
        $manager->persist($order);

        $manager->flush();
    }

    public function getDependencies(): array // phpcs:ignore
    {
        return [
            UserFixtures::class,
        ];
    }
}
