<?php

declare(strict_types=1);

namespace App\Component\Session\FlashType;

enum FlashTypes: string
{
    case SUCCESS = 'SUCCESS';
    case NOTICE = 'NOTICE';
    case INFO = 'INFO';
    case ERROR = 'ERROR';
}
