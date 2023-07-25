<?php

declare(strict_types=1);

namespace App\Bundle\JsonLogin\Service\User\Event;

use App\Bundle\JsonLogin\Service\Authorization\Token\Token;
use Symfony\Contracts\EventDispatcher\Event;

class UserAuthorizedEvent extends Event
{
    public const NAME = 'user.authorized';
    
    public function __construct(private readonly Token $token)
    {
    }

    public function getToken(): Token
    {
        return $this->token;
    }
}
