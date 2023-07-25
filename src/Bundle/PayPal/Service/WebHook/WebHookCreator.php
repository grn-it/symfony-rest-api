<?php

declare(strict_types=1);

namespace App\Bundle\PayPal\Service\WebHook;

use App\Bundle\PayPal\Client\PayPalClient;

class WebHookCreator
{
    public function __construct(private readonly PayPalClient $payPalClient)
    {
    }

    public function create(WebHookEvents $event, string $notificationUrl): WebHook
    {
        $createdWebHookDto = $this->payPalClient->createWebHook($event->value, $notificationUrl);

        return new WebHook($createdWebHookDto->getUuid());
    }
}
