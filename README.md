# Symfony REST API

Symfony application showing featues of a standard online shop with interaction with a payment gateway, services of a transport company and data exchange.

## Designed features
- Entities: User, Order, OrderProduct, Payment
- API: Order, OrderProduct
- Web Services: Payments and refunds through the payment gateway, Webhook handler when canceling a payment, Authorization
- Commands: Create payment gateway webhook for payment cancel event, Drop tables, Load fixtures 
- Data Fixtures: User, Product, PaymentStatus, PaymentType, OrderStatus
- Event Listener: Handling a business logic exception and converting to an HTTP exception
- Event Subscriber: Handling business logic events
- Migration: Creating tables for entities
- Docker: Services for the shop application, payment gateway, transport company and data exchange

```php
// Controller/Api/OrderProductApiController
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
```

```php
// Controller/WebService/OrderWebServiceController
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
```

```php
// Bundle/PayPal/Controller/WebService/PaymentWebServiceController
#[Route(path: '/service/paypal')]
class PaymentWebServiceController extends AbstractBaseController
{
    #[Route(
        path: '/payments/{id}/complete',
        name: 'app_web-service_paypal_payments_complete',
        methods: ['GET']
    )]
    public function complete(int $id, EventDispatcherInterface $dispatcher): Response
    {
        /** @var PaymentCompletedEvent $paymentCompletedEvent */
        $paymentCompletedEvent = $dispatcher->dispatch(
            new PaymentCompletedEvent($id),
            PaymentCompletedEvent::NAME
        );

        return $paymentCompletedEvent->getResponse();
    }

    /**
     * Handling webhook request from the PayPal payment gateway.
     */
    #[Route(
        path: '/payment/cancel',
        name: 'app_web-service_paypal_payment_cancel',
        methods: ['POST']
    )]
    public function cancel(Request $request, Serializer $serializer, EventDispatcherInterface $dispatcher): JsonResponse
    {
        $clientIp = $request->getClientIp();

        if (!$clientIp) {
            throw new BadRequestHttpException('Client IP must be set.');
        }

        if (!in_array($clientIp, IpWhitelist::getIps(), true)) {
            throw new BadRequestHttpException(
                sprintf('Client IP "%s" does not match the paypal payment gateway.', $clientIp)
            );
        }
        
        /** @var NotificationDto $notificationDto */
        $notificationDto = $serializer->deserialize($request->getContent(), NotificationDto::class);

        if ($notificationDto->getEvent() !== WebHookEvents::PAYMENT_CANCELED->value) {
            throw new HttpException(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                sprintf(
                    'Unexpected event "%s". Event must be equal to "%s".',
                    $notificationDto->getEvent(),
                    WebHookEvents::PAYMENT_CANCELED->value
                )
            );
        }

        $dispatcher->dispatch(
            new PaymentCanceledEvent($notificationDto->getUuid()),
            PaymentCanceledEvent::NAME
        );

        return new JsonResponse();
    }
}
```

```php
// Bundle/PayPal/Client/PayPalClient
class PayPalClient
{
    public const HOST = 'http://10.0.0.4';

    public function __construct(
        #[Autowire('%env(PAYMENT_GATEWAY_PAY_PAL_ACCESS_TOKEN)%')] private readonly string $accessToken,
        private readonly HttpClientInterface $httpClient,
        private readonly Serializer $serializer
    )
    {
    }

    public function getPayment(string $uuid): PaymentDto
    {
        $response = $this->request('POST', sprintf('%s/api/payments/%s', self::HOST, $uuid));

        return $this->serializer->deserialize($response->getContent(), PaymentDto::class);
    }

    /** @throws Throwable */
    public function createPayment(int $amount, string $returnUrl, string $description): CreatedPaymentDto
    {
        $createPaymentDto = new CreatePaymentDto($amount, $returnUrl, $description);
        
        $response = $this->request(
            'POST',
            self::HOST.'/service/payment/create',
            [
                'body' => $this->serializer->serialize($createPaymentDto)
            ]
        );

        /** @var CreatedPaymentDto $createdPaymentDto */
        $createdPaymentDto = $this->serializer->deserialize($response->getContent(), CreatedPaymentDto::class);

        $status = PaymentStatuses::tryFrom($createdPaymentDto->getStatus());

        if (!$status) {
            throw new EnumValueNotExistException(
                sprintf('Status "%s" does not exist.', $createdPaymentDto->getStatus())
            );
        }

        return $createdPaymentDto;
    }

    /** @throws Throwable */
    public function createRefund(string $uuid, string $description): CreatedRefundDto
    {
        $createRefundDto = new CreateRefundDto($uuid, $description);
        
        $response = $this->request(
            'POST',
            self::HOST.'/service/refund',
            [
                'body' => $this->serializer->serialize($createRefundDto)
            ]
        );
        
        /** @var CreatedRefundDto $createdRefundDto */
        $createdRefundDto = $this->serializer->deserialize($response->getContent(), CreatedRefundDto::class);

        $status = PaymentStatuses::tryFrom($createdRefundDto->getStatus());

        if (!$status) {
            throw new EnumValueNotExistException(
                sprintf('Status "%s" does not exist.', $createdRefundDto->getStatus())
            );
        }

        if ($createdRefundDto->getStatus() !== PaymentStatuses::PAYMENT_REFUND_COMPLETED->value) {
            throw new RefundStatusNotCompletedException('Refund payment status is not completed.');
        }

        return $createdRefundDto;
    }

    /** @throws Throwable */
    public function createWebHook(string $event, string $notificationUrl): CreatedWebHookDto
    {
        $createWebHookDto = new CreateWebHookDto($event, $notificationUrl);
        
        $response = $this->request(
            'POST',
            self::HOST.'/api/webhooks',
            [
                'body' => $this->serializer->serialize($createWebHookDto)
            ]
        );

        return $this->serializer->deserialize($response->getContent(), CreatedWebHookDto::class);
    }
    
    /** @param array<string, mixed> $options */
    private function request(string $method, string $url, array $options = []): ResponseInterface
    {
        try {
            $response = $this->httpClient->request(
                $method,
                $url,
                array_merge(
                    [
                        'headers' => [
                            'Authorization' => sprintf('Bearer %s', $this->accessToken),
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json'
                        ]
                    ],
                    $options
                )
            );

            if ($response->getStatusCode() !== Response::HTTP_OK
                && $response->getStatusCode() !== Response::HTTP_CREATED
            ) {
                throw new ResponseStatusNotSuccessfulException(
                    sprintf(
                        'Response status code is not successful. Status code: %d',
                        $response->getStatusCode()
                    )
                );
            }
        } catch (Throwable $e) {
            throw new PayPalClientException($e->getMessage());
        }
        
        return $response;
    }
}
```

```php
// Service/Payment/PaymentCreator
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
```
