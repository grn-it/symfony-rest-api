<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\PaymentType;
use App\Service\PaymentType\PaymentTypes;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PaymentTypeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $paymentType = new PaymentType();
        $paymentType->setName(strtolower(PaymentTypes::TRANSFER->name));
        $manager->persist($paymentType);

        $paymentType = new PaymentType();
        $paymentType->setName(strtolower(PaymentTypes::WITHDRAW->name));
        $manager->persist($paymentType);

        $manager->flush();
    }
}
