<?php

declare(strict_types=1);

namespace App\Bundle\PayPal\Controller\WebService;

use App\Bundle\PayPal\Security\Ip\IpWhitelist;
use App\Bundle\PayPal\Service\Notification\Dto\NotificationDto;
use App\Bundle\PayPal\Service\Payment\Event\PaymentCanceledEvent;
use App\Bundle\PayPal\Service\Payment\Event\PaymentCompletedEvent;
use App\Bundle\PayPal\Service\WebHook\WebHookEvents;
use App\Component\Controller\AbstractBaseController;
use App\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

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
