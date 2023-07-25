<?php

declare(strict_types=1);

namespace App\Bundle\PayPal\Service\PaymentStatus;

enum PaymentStatuses: string
{
    case PAYMENT_NEW = 'payment.new';
    case PAYMENT_COMPLETED = 'payment.completed';
    case PAYMENT_CANCELED = 'payment.canceled';
    case PAYMENT_REFUND_COMPLETED = 'payment.refund.completed';
}
