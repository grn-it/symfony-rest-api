<?php

declare(strict_types=1);

namespace App\Bundle\PayPal\Service\Payment\Event;

use App\Component\Exception\PropertyNotSetException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

class PaymentCompletedEvent extends Event
{
    public const NAME = 'payment.completed';
    
    private ?Response $response = null;

    public function __construct(private readonly int $id)
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getResponse(): Response
    {
        if (!$this->response) {
            throw new PropertyNotSetException('Property "response" must be set.');
        }

        return $this->response;
    }

    public function setResponse(Response $response): self
    {
        $this->response = $response;

        return $this;
    }
}
