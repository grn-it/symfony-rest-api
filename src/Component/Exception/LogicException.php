<?php

declare(strict_types=1);

namespace App\Component\Exception;

/**
 * Error on the server side.
 * An exception for cases when the logic of the code is not correct.
 * Requires a fix by the server-side developer.
 */
// phpcs:ignore
class LogicException extends \LogicException
{
}
