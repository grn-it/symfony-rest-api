<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Component\Controller\AbstractBaseController;
use App\Component\Serializer\Serializer;
use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Service\Order\Dto\DeletedOrderDto;
use App\Service\Order\Dto\Factory\OrderDtoFactory;
use App\Service\Order\Dto\OrderDto;
use App\Service\Order\Event\OrderBeforeShowEvent;
use App\Service\Order\Exception\OrderNewAlreadyExistException;
use App\Service\Order\Exception\OrderNotCurrentException;
use App\Service\Order\OrderCreator;
use App\Service\Order\OrderRemover;
use App\Service\Order\Voter\OrderVoter;
use App\Service\OrderStatus\OrderStatuses;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route('/api/orders')]
class OrderApiController extends AbstractBaseController
{
    #[Route(
        name: 'app_api_orders_create',
        methods: ['POST']
    )]
    public function create(
        OrderCreator $orderCreator,
        OrderDtoFactory $orderDtoFactory,
        Serializer $serializer
    ): JsonResponse
    {
        try {
            $order = $orderCreator->create(OrderStatuses::NEW, $this->getCurrentUser());
        } catch (OrderNewAlreadyExistException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $orderDto = $orderDtoFactory->create($order, OrderDto::class);

        return new JsonResponse($serializer->serialize($orderDto), Response::HTTP_CREATED, json: true);
    }

    #[Route(
        name: 'app_api_orders_get-list',
        methods: ['GET']
    )]
    public function getList(
        OrderRepository $orderRepository,
        EventDispatcherInterface $dispatcher,
        OrderDtoFactory $orderDtoFactory,
        Serializer $serializer
    ): JsonResponse
    {
        $orders = $orderRepository->findOrders(
            statuses: $this->getQueryParameterInt('status'),
            user: $this->getCurrentUser()->getId()
        );

        $dispatcher->dispatch(new OrderBeforeShowEvent($orders), OrderBeforeShowEvent::NAME);
        
        $orderDtos = [];

        foreach ($orders as $order) {
            $orderDtos[] = $orderDtoFactory->create($order, OrderDto::class);
        }

        return new JsonResponse($serializer->serialize($orderDtos), json: true);
    }

    #[Route(
        path: '/{id}',
        name: 'app_api_orders_get-item',
        methods: ['GET']
    )]
    public function getItem(
        Order $order,
        EventDispatcherInterface $dispatcher,
        OrderDtoFactory $orderDtoFactory,
        Serializer $serializer
    ): JsonResponse
    {
        $this->denyAccessUnlessGranted(OrderVoter::VIEW, $order);
        
        $dispatcher->dispatch(new OrderBeforeShowEvent($order), OrderBeforeShowEvent::NAME);
        
        $orderDto = $orderDtoFactory->create($order, OrderDto::class);

        return new JsonResponse($serializer->serialize($orderDto), json: true);
    }

    #[Route(
        path: '/{id}',
        name: 'app_api_orders_remove',
        methods: ['DELETE']
    )]
    public function remove(
        Order $order,
        OrderRemover $orderRemover,
        EventDispatcherInterface $dispatcher,
        OrderDtoFactory $orderDtoFactory,
        Serializer $serializer
    ): JsonResponse
    {
        $this->denyAccessUnlessGranted(OrderVoter::EDIT, $order);
        
        try {
            $orderRemover->remove($order);
        } catch (OrderNotCurrentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $dispatcher->dispatch(new OrderBeforeShowEvent($order), OrderBeforeShowEvent::NAME);

        $deletedOrderDto = $orderDtoFactory->create($order, DeletedOrderDto::class);

        return new JsonResponse($serializer->serialize($deletedOrderDto), json: true);
    }
}
