<?php

declare(strict_types=1);

namespace App\Service\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\User\Exception\UserNotHaveRoleException;
use App\Service\User\Role\UserRoles;

class UserRemover
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }
    
    public function removeGuest(User $user): void
    {
        if (!in_array(UserRoles::GUEST->value, $user->getRoles(), true)) {
            throw new UserNotHaveRoleException(
                sprintf('Only user with role "%s" can be removed.', UserRoles::GUEST->name)
            );
        }

        $this->userRepository->remove($user);
    }
}
