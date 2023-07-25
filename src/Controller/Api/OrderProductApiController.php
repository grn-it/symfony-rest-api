<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Component\Controller\AbstractBaseController;
use App\Component\Exception\EntityNotExistException;
use App\Component\Serializer\Serializer;
use App\Component\Validator\Validator;
use App\Entity\OrderProduct;
use App\Repository\OrderProductRepository;
use App\Repository\OrderRepository;
use App\Service\Order\Exception\OrderNotBelongToUserException;
use App\Service\Order\Exception\OrderNotCurrentException;
use App\Service\Order\Voter\OrderVoter;
use App\Service\OrderProduct\Dto\CreateOrderProductDto;
use App\Service\OrderProduct\Dto\DeletedOrderProductDto;
use App\Service\OrderProduct\Dto\Factory\OrderProductDtoFactory;
use App\Service\OrderProduct\Dto\OrderProductDto;
use App\Service\OrderProduct\Dto\UpdateOrderProductDto;
use App\Service\OrderProduct\Event\OrderProductBeforeShowEvent;
use App\Service\OrderProduct\OrderProductCreator;
use App\Service\OrderProduct\OrderProductRemover;
use App\Service\OrderProduct\OrderProductUpdater;
use App\Service\OrderProduct\Voter\OrderProductVoter;
use App\Service\Product\Exception\ProductAlreadyInOrderException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route('/api/order-products')]
class OrderProductApiController extends AbstractBaseController
{
    #[Route(
        name: 'app_api_order-products_create',
        methods: ['POST']
    )]
    public function create(
        Serializer $serializer,
        Validator $validator,
        OrderProductDtoFactory $orderProductDtoFactory,
        Request $request,
        OrderRepository $orderRepository,
        OrderProductCreator $orderProductCreator,
        EventDispatcherInterface $dispatcher
    ): JsonResponse
    {
        /** @var CreateOrderProductDto $createOrderProductDto */
        $createOrderProductDto = $serializer->deserialize($request->getContent(), CreateOrderProductDto::class);

        $validator->validate($createOrderProductDto);

        try {
            $this->denyAccessUnlessGranted(
                OrderVoter::EDIT,
                $orderRepository->get($createOrderProductDto->getOrder()->getId())
            );

            $orderProduct = $orderProductCreator->create(
                $createOrderProductDto->getOrder()->getId(),
                $createOrderProductDto->getProduct()->getId(),
                $createOrderProductDto->getQuantity(),
                $this->getCurrentUser()
            );
        } catch (
            OrderNotCurrentException|
            ProductAlreadyInOrderException|
            EntityNotExistException|
            OrderNotBelongToUserException
            $e
        ) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $dispatcher->dispatch(new OrderProductBeforeShowEvent($orderProduct), OrderProductBeforeShowEvent::NAME);

        $orderProductDto = $orderProductDtoFactory->create($orderProduct, OrderProductDto::class);

        return new JsonResponse($serializer->serialize($orderProductDto), Response::HTTP_CREATED, json: true);
    }

    #[Route(
        name: 'app_api_order-products_get-list',
        methods: ['GET']
    )]
    public function getList(
        OrderProductRepository $orderProductRepository,
        EventDispatcherInterface $dispatcher,
        OrderProductDtoFactory $orderProductDtoFactory,
        Serializer $serializer
    ): JsonResponse
    {
        $orderProducts = $orderProductRepository->findOrderProducts(
            order: $this->getQueryParameterInt('order'),
            user: $this->getCurrentUser()->getId()
        );

        $dispatcher->dispatch(new OrderProductBeforeShowEvent($orderProducts), OrderProductBeforeShowEvent::NAME);

        $orderProductDtos = [];

        foreach ($orderProducts as $orderProduct) {
            $orderProductDtos[] = $orderProductDtoFactory->create($orderProduct, OrderProductDto::class);
        }

        return new JsonResponse($serializer->serialize($orderProductDtos), json: true);
    }

    #[Route(
        path: '/{id}',
        name: 'app_api_order-products_get-item',
        methods: ['GET']
    )]
    public function getItem(
        OrderProduct $orderProduct,
        EventDispatcherInterface $dispatcher,
        OrderProductDtoFactory $orderProductDtoFactory,
        Serializer $serializer
    ): JsonResponse
    {
        $this->denyAccessUnlessGranted(OrderProductVoter::VIEW, $orderProduct);
        
        $dispatcher->dispatch(new OrderProductBeforeShowEvent($orderProduct), OrderProductBeforeShowEvent::NAME);

        $orderProductDto = $orderProductDtoFactory->create($orderProduct, OrderProductDto::class);

        return new JsonResponse($serializer->serialize($orderProductDto), json: true);
    }

    #[Route(
        path: '/{id}',
        name: 'app_api_order-products_update',
        methods: ['PUT']
    )]
    public function update(
        Serializer $serializer,
        Validator $validator,
        OrderProduct $orderProduct,
        OrderProductDtoFactory $orderProductDtoFactory,
        Request $request,
        OrderProductUpdater $orderProductUpdater,
        EventDispatcherInterface $dispatcher
    ): JsonResponse
    {
        $this->denyAccessUnlessGranted(OrderProductVoter::EDIT, $orderProduct);
        
        /** @var UpdateOrderProductDto $updateOrderProductDto */
        $updateOrderProductDto = $serializer->deserialize($request->getContent(), UpdateOrderProductDto::class);

        $validator->validate($updateOrderProductDto);

        try {
            $orderProductUpdater->updateQuantity($orderProduct, $updateOrderProductDto->getQuantity());
        } catch (OrderNotCurrentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        
        $dispatcher->dispatch(new OrderProductBeforeShowEvent($orderProduct), OrderProductBeforeShowEvent::NAME);

        $orderProductDto = $orderProductDtoFactory->create($orderProduct, OrderProductDto::class);

        return new JsonResponse($serializer->serialize($orderProductDto), json: true);
    }

    #[Route(
        path: '/{id}',
        name: 'app_api_order-products_remove',
        methods: ['DELETE']
    )]
    public function remove(
        OrderProduct $orderProduct,
        OrderProductRemover $orderProductRemover,
        EventDispatcherInterface $dispatcher,
        OrderProductDtoFactory $orderProductDtoFactory,
        Serializer $serializer
    ): JsonResponse
    {
        $this->denyAccessUnlessGranted(OrderProductVoter::EDIT, $orderProduct);
        
        try {
            $orderProductRemover->remove($orderProduct);
        } catch (OrderNotCurrentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $dispatcher->dispatch(new OrderProductBeforeShowEvent($orderProduct), OrderProductBeforeShowEvent::NAME);

        $deletedOrderProductDto = $orderProductDtoFactory->create($orderProduct, DeletedOrderProductDto::class);

        return new JsonResponse($serializer->serialize($deletedOrderProductDto), json: true);
    }
}
