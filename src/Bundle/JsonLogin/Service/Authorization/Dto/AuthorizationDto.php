<?php

declare(strict_types=1);

namespace App\Bundle\JsonLogin\Service\Authorization\Dto;

class AuthorizationDto
{
    public function __construct(private readonly bool $authorized, private readonly string $message)
    {
    }
}
