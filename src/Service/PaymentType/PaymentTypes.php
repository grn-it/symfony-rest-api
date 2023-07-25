<?php

declare(strict_types=1);

namespace App\Service\PaymentType;

enum PaymentTypes: int
{
    case TRANSFER = 1;
    case WITHDRAW = 2;
}
