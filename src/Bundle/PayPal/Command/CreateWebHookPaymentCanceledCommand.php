<?php

declare(strict_types=1);

namespace App\Bundle\PayPal\Command;

use App\Bundle\PayPal\Service\WebHook\WebHookCreator;
use App\Bundle\PayPal\Service\WebHook\WebHookEvents;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\RouterInterface;
use Throwable;

#[AsCommand(
    name: 'app:paypal:webhook:payment-canceled:create',
    description: 'Creates webhook for event "payment.canceled" in PayPal payment gateway.'
)]
class CreateWebHookPaymentCanceledCommand extends Command
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly WebHookCreator $webHookCreator
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $webHook = $this->webHookCreator->create(
                WebHookEvents::PAYMENT_CANCELED,
                $this->router->generate(
                    'app_web-service_payment_cancel'
                )
            );
        } catch (Throwable $e) {
            $io->error(
                sprintf('Failed. Details: %s', $e->getMessage())
            );

            return Command::FAILURE;
        }

        $io->success(
            sprintf(
                'Webhook "%s" for event "%s" in PayPal payment gateway created.',
                $webHook->getUuid(),
                WebHookEvents::PAYMENT_CANCELED->value
            )
        );

        return Command::SUCCESS;
    }
}
