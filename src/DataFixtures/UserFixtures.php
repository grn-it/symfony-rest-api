<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use App\Service\User\Role\UserRoles;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/** @psalm-suppress PropertyNotSetInConstructor */
class UserFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $defaultPassword = 'p@ssw0rd';

        $user = new User();
        $user->setFirstname('James');
        $user->setLastname('Smith');
        $user->setEmail('james.smith@gmail.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, $defaultPassword));
        $user->setRoles([UserRoles::USER->value]);
        $user->setPhoneNumber('89058092986');
        $manager->persist($user);

        $user = new User();
        $user->setFirstname('Oliver');
        $user->setLastname('Williams');
        $user->setEmail('oliver.williams@gmail.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, $defaultPassword));
        $user->setRoles([UserRoles::ADMIN->value]);
        $user->setPhoneNumber('89117092923');
        $manager->persist($user);

        $manager->flush();
    }
}
