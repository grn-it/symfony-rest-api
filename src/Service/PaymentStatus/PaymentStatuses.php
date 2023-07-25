<?php

declare(strict_types=1);

namespace App\Service\PaymentStatus;

enum PaymentStatuses: int
{
    case NEW = 1;
    case COMPLETED = 2;
    case CANCELED = 3;
}
