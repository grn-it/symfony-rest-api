<?php

declare(strict_types=1);

namespace App\Bundle\PayPal\Client;

use App\Bundle\PayPal\Client\Exception\PayPalClientException;
use App\Bundle\PayPal\Client\Exception\ResponseStatusNotSuccessfulException;
use App\Bundle\PayPal\Service\Payment\Dto\CreatedPaymentDto;
use App\Bundle\PayPal\Service\Payment\Dto\CreatePaymentDto;
use App\Bundle\PayPal\Service\Payment\Dto\PaymentDto;
use App\Bundle\PayPal\Service\PaymentStatus\PaymentStatuses;
use App\Bundle\PayPal\Service\Refund\Dto\CreatedRefundDto;
use App\Bundle\PayPal\Service\Refund\Dto\CreateRefundDto;
use App\Bundle\PayPal\Service\Refund\Exception\RefundStatusNotCompletedException;
use App\Bundle\PayPal\Service\WebHook\Dto\CreatedWebHookDto;
use App\Bundle\PayPal\Service\WebHook\Dto\CreateWebHookDto;
use App\Component\Exception\EnumValueNotExistException;
use App\Component\Serializer\Serializer;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Throwable;

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
