<?php

declare(strict_types=1);

namespace App\Bundle\PayPal\Service\WebHook;

class WebHook
{
    public function __construct(private readonly string $uuid)
    {
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }
}
