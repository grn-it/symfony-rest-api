<?php

declare(strict_types=1);

namespace App\Bundle\PayPal\Service\WebHook\Dto;

class CreatedWebHookDto
{
    public function __construct(private readonly string $uuid)
    {
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }
}
