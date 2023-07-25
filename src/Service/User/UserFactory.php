<?php

declare(strict_types=1);

namespace App\Service\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\User\Role\UserRoles;
use InvalidArgumentException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFactory
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher
    )
    {
    }
    
    /** @param array<string, string> $context */
    public function create(string $role, array $context = []): User
    {
        switch ($role) {
            case 'ROLE_GUEST':
                $session = $context['session'] ?? null;

                if (empty($session)) {
                    throw new InvalidArgumentException('Item "session" must be set.');
                }

                $user = $this->createGuestUser($session);
                
                break;
            default:
                throw new InvalidArgumentException(
                    sprintf('Role "%s" is not supported.', $role)
                );
        }

        return $user;
    }

    private function createGuestUser(string $session): User
    {
        $user = new User();

        $user->setFirstname('');
        $user->setLastname('');
        $user->setEmail('guest-'.$session.'@app.com');
        $user->setSession($session);
        $user->setRoles([UserRoles::GUEST->value]);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'guest'));

        $this->userRepository->save($user);

        return $user;
    }
}
