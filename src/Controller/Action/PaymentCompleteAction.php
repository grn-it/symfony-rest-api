<?php

declare(strict_types=1);

namespace App\Controller\Action;

use App\Bundle\PayPal\Client\PayPalClient;
use App\Bundle\PayPal\Service\PaymentStatus\PaymentStatuses as PayPalPaymentStatuses;
use App\Component\Controller\AbstractBaseController;
use App\Repository\PaymentRepository;
use App\Service\Payment\PaymentManager;
use App\Service\Payment\Voter\PaymentVoter;
use App\Service\PaymentStatus\PaymentStatuses;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PaymentCompleteAction extends AbstractBaseController
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly PaymentManager $paymentManager,
        private readonly PaymentRepository $paymentRepository,
        private readonly PayPalClient $payPalClient,
        private readonly LoggerInterface $logger
    )
    {
        parent::__construct($requestStack);
    }

    public function complete(int $id): Response
    {
        $payment = $this->paymentRepository->findPayment(id: $id);

        if (!$payment) {
            return new Response(status: Response::HTTP_NOT_FOUND);
        }

        try {
            $this->denyAccessUnlessGranted(PaymentVoter::EDIT, $payment);
        } catch (Throwable) {
            return new Response(status: Response::HTTP_FORBIDDEN);
        }
        
        switch ($payment->getStatus()->getId()) {
            case PaymentStatuses::COMPLETED->value:
                return $this->redirectToRoute(
                    'app_web-page_orders_show_item',
                    ['id' => $payment->getOrder()->getId()]
                );
            case PaymentStatuses::CANCELED->value:
                $this->addFlash('order.info', 'Payment canceled.');

                return $this->redirectToRoute(
                    'app_web-page_orders_show_item',
                    ['id' => $payment->getOrder()->getId()]
                );
        }

        $payPalPaymentDto = $this->payPalClient->getPayment($payment->getUuid());

        if ($payPalPaymentDto->getStatus() !== PayPalPaymentStatuses::PAYMENT_COMPLETED->value) {
            return new Response(status: Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->paymentManager->completed($payment);
        } catch (Throwable $e) {
            $this->logger->critical($e);

            $this->addFlash('order.error', 'Confirm payment failed. Contact support.');

            return $this->redirectToRoute('app_web-page_order_show');
        }

        return $this->redirectToRoute(
            'app_web-page_orders_show_item',
            ['id' => $payment->getOrder()->getId()]
        );
    }
}
