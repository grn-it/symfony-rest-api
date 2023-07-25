<?php

declare(strict_types=1);

namespace App\Command\Database;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

// phpcs:ignore
#[AsCommand(
    name: 'app:database:fixtures:load',
    description: 'Purge database, restart sequences (PostgreSQL) and load fixtures'
)]
class LoadFixturesCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Purge database, restart sequences (PostgreSQL) and load fixtures');

        if ($input->isInteractive()) {
            // phpcs:ignore
            if (!$io->confirm(
                "Careful, database will be purged and sequences (PostgreSQL) restarted.\n Do you want to continue?",
                false
            )
            ) {
                return Command::SUCCESS;
            }
        }

        $io->section('Restarting sequences (PostgreSQL)');

        /** @var array<int, string> $sequences */
        $sequences = $this->em
            ->getConnection()
            ->executeQuery("SELECT c.relname FROM pg_class c WHERE (c.relkind = 'S')")
            ->fetchFirstColumn()
        ;

        foreach ($sequences as $sequence) {
            $this->em->getConnection()
                ->executeQuery('ALTER SEQUENCE '.$sequence.' RESTART WITH 1');

            $io->writeln($sequence);
        }

        $io->section('Purging database and loading fixtures');

        $application = $this->getApplication();

        if (!$application) {
            return Command::FAILURE;
        }

        $command = $application->find('doctrine:fixtures:load');

        $arrayInput = new ArrayInput([]);
        $arrayInput->setInteractive(false);

        $returnCode = $command->run($arrayInput, $output);

        if (Command::SUCCESS !== $returnCode) {
            return $returnCode;
        }

        $io->newLine(2);
        $io->success('Fixtures loaded');

        return Command::SUCCESS;
    }
}
