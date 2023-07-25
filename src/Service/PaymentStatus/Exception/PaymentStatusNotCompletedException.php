<?php

declare(strict_types=1);

namespace App\Service\PaymentStatus\Exception;

use App\Component\Exception\LogicException;

class PaymentStatusNotCompletedException extends LogicException
{
}
