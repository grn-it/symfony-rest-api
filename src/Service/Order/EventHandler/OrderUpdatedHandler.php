<?php

declare(strict_types=1);

namespace App\Service\Order\EventHandler;

use App\Entity\Order;
use DateTime;

class OrderUpdatedHandler
{
    public function handle(Order $order): void
    {
        $order->setUpdatedAt(new DateTime());
    }
}
