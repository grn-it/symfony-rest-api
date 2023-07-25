<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\PaymentStatus;
use App\Service\PaymentStatus\PaymentStatuses;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PaymentStatusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $paymentStatus = new PaymentStatus();
        $paymentStatus->setName(strtolower(PaymentStatuses::NEW->name));
        $manager->persist($paymentStatus);

        $paymentStatus = new PaymentStatus();
        $paymentStatus->setName(strtolower(PaymentStatuses::COMPLETED->name));
        $manager->persist($paymentStatus);

        $paymentStatus = new PaymentStatus();
        $paymentStatus->setName(strtolower(PaymentStatuses::CANCELED->name));
        $manager->persist($paymentStatus);

        $manager->flush();
    }
}
