<?php

declare(strict_types=1);

namespace App\Service\Payment\Exception;

use App\Component\Exception\LogicException;

class PaymentAlreadyExistException extends LogicException
{
}
