<?php

declare(strict_types=1);

namespace App\Bundle\PayPal\Security\Ip;

class IpWhitelist
{
    /** @return array<string> */
    public static function getIps(): array
    {
        return ['10.0.0.4'];
    }
}
