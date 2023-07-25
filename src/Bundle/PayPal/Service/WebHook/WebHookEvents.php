<?php

declare(strict_types=1);

namespace App\Bundle\PayPal\Service\WebHook;

enum WebHookEvents: string
{
    case PAYMENT_COMPLETED = 'payment.completed';
    case PAYMENT_CANCELED = 'payment.canceled';
}
