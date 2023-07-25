<?php

declare(strict_types=1);

namespace App\Controller\WebPage;

use App\Component\Controller\AbstractBaseController;
use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Service\Order\Voter\OrderVoter;
use App\Service\OrderStatus\OrderStatuses;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderWebPageController extends AbstractBaseController
{
    #[Route(
        path: '/order',
        name: 'app_web-page_order_show',
        methods: ['GET']
    )]
    public function showCurrent(OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->findOrder(
            statuses: OrderStatuses::getCurrent(),
            user: $this->getCurrentUser()->getId()
        );

        if (!$order) {
            throw $this->createNotFoundException();
        }

        if (!$order->getOrderProducts()->count()) {
            return $this->render('Order/empty.html.twig');
        }

        if ($order->getStatus()->getId() === OrderStatuses::PLACED->value) {
            return $this->render('Order/placed.html.twig');
        }

        return $this->render('Order/new.html.twig');
    }

    #[Route(
        path: '/orders/{id}',
        name: 'app_web-page_orders_show_item',
        methods: ['GET']
    )]
    public function showItem(Order $order): Response
    {
        $this->denyAccessUnlessGranted(OrderVoter::VIEW, $order);

        return $this->render('Order/item.html.twig', ['order' => $order]);
    }
}
