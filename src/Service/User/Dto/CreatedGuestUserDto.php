<?php

declare(strict_types=1);

namespace App\Service\User\Dto;

class CreatedGuestUserDto
{
    public function __construct(private readonly int $id, private readonly string $email)
    {
    }
}
