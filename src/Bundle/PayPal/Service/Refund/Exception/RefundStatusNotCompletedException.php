<?php

declare(strict_types=1);

namespace App\Bundle\PayPal\Service\Refund\Exception;

use App\Component\Exception\LogicException;

class RefundStatusNotCompletedException extends LogicException
{
}
