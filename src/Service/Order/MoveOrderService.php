<?php

declare(strict_types=1);

namespace App\Service\Order;

use App\Entity\User;
use App\Repository\OrderRepository;
use App\Service\OrderStatus\OrderStatuses;
use App\Service\User\Exception\UserNotHaveRoleException;
use App\Service\User\Role\UserRoles;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(lazy: true)]
class MoveOrderService
{
    public function __construct(private readonly OrderRepository $orderRepository)
    {
    }

    public function moveCurrentOrderFromGuestToUser(User $guest, User $user): void
    {
        if (!in_array(UserRoles::GUEST->value, $guest->getRoles(), true)) {
            throw new UserNotHaveRoleException(
                sprintf('User must have "%s" role.', UserRoles::GUEST->name)
            );
        }

        if (!in_array(UserRoles::USER->value, $user->getRoles(), true)) {
            throw new UserNotHaveRoleException(
                sprintf('User must have "%s" role.', UserRoles::USER->name)
            );
        }

        $guestCurrentOrder = $this->orderRepository->findOrder(
            statuses: OrderStatuses::getCurrent(),
            user: $guest->getId()
        );

        if (!$guestCurrentOrder) {
            return;
        }

        $userCurrentOrder = $this->orderRepository->findOrder(
            statuses: OrderStatuses::getCurrent(),
            user: $user->getId()
        );

        if ($userCurrentOrder) {
            $this->orderRepository->remove($userCurrentOrder);
        }

        $guestCurrentOrder->setUser($user);

        $this->orderRepository->save($guestCurrentOrder);
    }
}
