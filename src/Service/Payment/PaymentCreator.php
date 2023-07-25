<?php

declare(strict_types=1);

namespace App\Service\Payment;

use App\Entity\Order;
use App\Entity\Payment;
use App\Repository\PaymentRepository;
use App\Repository\PaymentStatusRepository;
use App\Repository\PaymentTypeRepository;
use App\Service\Payment\Event\PaymentCreatedEvent;
use App\Service\Payment\Exception\PaymentAlreadyExistException;
use App\Service\Payment\Exception\PaymentAmountNotPositiveException;
use App\Service\PaymentStatus\PaymentStatuses;
use App\Service\PaymentType\PaymentTypes;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class PaymentCreator
{
    public function __construct(
        private readonly PaymentRepository $paymentRepository,
        private readonly PaymentTypeRepository $paymentTypeRepository,
        private readonly PaymentStatusRepository $paymentStatusRepository,
        private readonly EventDispatcherInterface $dispatcher
    )
    {
    }
    
    public function create(Order $order, int $amount, PaymentStatuses $status, PaymentTypes $type): Payment
    {
        if ($this->paymentRepository->isExist($order->getId(), $status->value, $type->value)) {
            throw new PaymentAlreadyExistException('Payment already exist.');
        }

        if ($amount === 0) {
            throw new PaymentAmountNotPositiveException('Payment amount must be greater than zero.');
        }

        $payment = new Payment();

        $payment->setOrder($order);
        $payment->setAmount($amount);
        $payment->setType($this->paymentTypeRepository->get($type->value));
        $payment->setStatus($this->paymentStatusRepository->get($status->value));

        $this->paymentRepository->save($payment);

        $this->dispatcher->dispatch(new PaymentCreatedEvent($payment), PaymentCreatedEvent::NAME);

        return $payment;
    }
}
