<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Component\Serializer\Exception\SerializerException;
use App\Component\Validator\Exception\ValidatorException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsEventListener]
class ExceptionListener
{
    public function __construct(
        #[Autowire('%kernel.debug%')] private readonly bool $debug,
        private readonly LoggerInterface $logger
    ) {
    }
    
    public function __invoke(ExceptionEvent $event): void
    {
        $acceptableContentTypes = $event->getRequest()->getAcceptableContentTypes();
        
        if (in_array('application/json', $acceptableContentTypes, true)) {
            $exception = $event->getThrowable();

            switch ($exception::class) {
                case SerializerException::class:
                    if (!$this->debug) {
                        throw new BadRequestHttpException('Serialization error: incorrect json data structure.');
                    }

                    break;
                case ValidatorException::class:
                    if (!$this->debug) {
                        throw new BadRequestHttpException('Validation error: json data not valid.');
                    }

                    break;
            }
        }
    }
}
