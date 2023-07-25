<?php

declare(strict_types=1);

namespace App\Service\Order;

use App\Entity\Order;
use App\Entity\User;

class OrderManager
{
    public function __construct(
        private readonly SumOrderService $sumOrderService,
        private readonly PayOrderService $payOrderService,
        private readonly MoveOrderService $moveOrderService,
        private readonly RefundOrderService $refundOrderService,
        private readonly PaidOrderService $paidOrderService
    )
    {
    }

    public function calculateSum(Order $order): void
    {
        $this->sumOrderService->calculateSum($order);
    }

    public function moveCurrentOrderFromGuestToUser(User $guest, User $user): void
    {
        $this->moveOrderService->moveCurrentOrderFromGuestToUser($guest, $user);
    }

    public function pay(Order $order): PayOrder
    {
        return $this->payOrderService->pay($order);
    }

    public function paid(Order $order): void
    {
        $this->paidOrderService->paid($order);
    }

    public function refund(Order $order): void
    {
        $this->refundOrderService->refund($order);
    }
}
