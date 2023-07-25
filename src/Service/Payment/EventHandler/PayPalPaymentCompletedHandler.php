<?php

declare(strict_types=1);

namespace App\Service\Payment\EventHandler;

use App\Controller\Action\PaymentCompleteAction;
use App\Repository\PaymentRepository;
use App\Service\Payment\PaymentManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class PayPalPaymentCompletedHandler
{
    public function __construct(
        private readonly PaymentRepository $paymentRepository,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly RouterInterface $router,
        private readonly RequestStack $requestStack,
        private readonly PaymentManager $paymentManager,
        private readonly LoggerInterface $logger,
        private readonly PaymentCompleteAction $paymentCompleteAction
    )
    {
    }

    public function handle(int $id): Response
    {
        return $this->paymentCompleteAction->complete($id);
    }
}
