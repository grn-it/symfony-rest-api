<?php

declare(strict_types=1);

namespace App\Service\Order\EventHandler;

use App\Bundle\JsonLogin\Service\Authorization\Token\Token;
use App\Component\Exception\EntityNotExistException;
use App\Repository\UserRepository;
use App\Service\Order\OrderManager;
use App\Service\User\UserRemover;

class UserAuthorizedHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly OrderManager $orderManager,
        private readonly UserRemover $userRemover
    )
    {
    }

    public function handle(Token $token): void
    {
        $session = $token->getSession();

        if (!$session) {
            return;
        }

        try {
            $guest = $this->userRepository->get(session: $session);
            $user = $this->userRepository->get(email: $token->getUser()->getUserIdentifier());

            $this->orderManager->moveCurrentOrderFromGuestToUser($guest, $user);
            $this->userRemover->removeGuest($guest);
        } catch (EntityNotExistException) {
        }
    }
}
