<?php

declare(strict_types=1);

namespace App\Bundle\JsonLogin\Service\Authorization\Token;

use Symfony\Component\Security\Core\User\UserInterface;

class Token
{
    public function __construct(private readonly UserInterface $user, private readonly ?string $session = null)
    {
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getSession(): ?string
    {
        return $this->session;
    }
}
