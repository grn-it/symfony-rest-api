<?php

declare(strict_types=1);

namespace App\Service\Order\EventSubscriber;

use App\Bundle\JsonLogin\Service\User\Event\UserAuthorizedEvent;
use App\Service\Order\EventHandler\UserAuthorizedHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserAuthorizedSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly UserAuthorizedHandler $userAuthorizedHandler)
    {
    }

    public function onUserAuthorized(UserAuthorizedEvent $event): void
    {
        $this->userAuthorizedHandler->handle($event->getToken());
    }
    
    /** @return array<string, string> */
    public static function getSubscribedEvents(): array
    {
        return [
            UserAuthorizedEvent::NAME => 'onUserAuthorized'
        ];
    }
}
