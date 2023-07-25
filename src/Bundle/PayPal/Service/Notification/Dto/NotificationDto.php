<?php

declare(strict_types=1);

namespace App\Bundle\PayPal\Service\Notification\Dto;

class NotificationDto
{
    public function __construct(private readonly string $uuid, private readonly string $event)
    {
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getEvent(): string
    {
        return $this->event;
    }
}
