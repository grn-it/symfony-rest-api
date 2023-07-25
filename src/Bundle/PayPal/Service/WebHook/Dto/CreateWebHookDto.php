<?php

declare(strict_types=1);

namespace App\Bundle\PayPal\Service\WebHook\Dto;

class CreateWebHookDto
{
    public function __construct(private readonly string $event, private readonly string $notificationUrl)
    {
    }
}
