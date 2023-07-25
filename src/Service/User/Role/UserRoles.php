<?php

declare(strict_types=1);

namespace App\Service\User\Role;

enum UserRoles: string
{
    case GUEST = 'ROLE_GUEST';
    case USER = 'ROLE_USER';
    case MANAGER = 'ROLE_MANAGER';
    case ADMIN = 'ROLE_ADMIN';
}
