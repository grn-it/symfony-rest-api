<?php

declare(strict_types=1);

namespace App\Service\Order\Exception;

use App\Component\Exception\LogicException;

class OrderNotCurrentException extends LogicException
{
}
