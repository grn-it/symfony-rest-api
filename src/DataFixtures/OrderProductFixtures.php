<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class OrderProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /** @var Product $product */
        $product = $manager->getRepository(Product::class)->find(1);

        $orderProduct = new OrderProduct();
        $orderProduct->setOrder($manager->getRepository(Order::class)->find(1));
        $orderProduct->setProduct($product);
        $orderProduct->setQuantity(1);
        $orderProduct->setName($product->getName());
        $orderProduct->setDescription($product->getDescription());
        $manager->persist($orderProduct);

        $manager->flush();
    }

    public function getDependencies(): array // phpcs:ignore
    {
        return [
            OrderFixtures::class,
            ProductFixtures::class
        ];
    }
}
