<?php

declare(strict_types=1);

namespace App\Controller\WebService;

use App\Component\Controller\AbstractBaseController;
use App\Entity\Order;
use App\Service\Order\OrderManager;
use App\Service\Order\Voter\OrderVoter;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

#[Route(path: '/service/orders')]
class OrderWebServiceController extends AbstractBaseController
{
    #[Route(
        path: '/{id}/pay',
        name: 'app_web-service_order_pay',
        methods: ['GET']
    )]
    public function pay(Order $order, OrderManager $orderManager, LoggerInterface $logger): Response
    {
        $this->denyAccessUnlessGranted(OrderVoter::EDIT, $order);

        try {
            $payOrder = $orderManager->pay($order);
        } catch (Throwable $e) {
            $logger->critical($e);

            $this->addFlash('order.error', 'Payment failed. Try later.');

            return $this->redirectToRoute('app_web-page_order_show');
        }

        return $this->redirect($payOrder->getConfirmationUrl());
    }

    #[Route(
        path: '/{id}/refund',
        name: 'app_web-service_order_refund',
        methods: ['GET']
    )]
    public function refund(Order $order, LoggerInterface $logger, OrderManager $orderManager): Response
    {
        $this->denyAccessUnlessGranted(OrderVoter::EDIT, $order);
        
        try {
            $orderManager->refund($order);
    
            $this->addFlash('order.success', 'Payment refund completed.');
        } catch (Throwable $e) {
            $logger->critical($e);

            $this->addFlash('order.error', 'Payment refund failed. Contact support.');

            return $this->redirectToRoute(
                'app_web-page_orders_show_item',
                ['id' => $order->getId()]
            );
        }
        
        return $this->redirectToRoute(
            'app_web-page_orders_show_item',
            ['id' => $order->getId()]
        );
    }
}
