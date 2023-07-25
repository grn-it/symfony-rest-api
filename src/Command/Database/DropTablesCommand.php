<?php

declare(strict_types=1);

namespace App\Command\Database;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

// phpcs:ignore
#[AsCommand(
    name: 'app:database:tables:drop',
    description: 'Drop all tables and sequences (PostgreSQL)'
)]
class DropTablesCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Drop all tables and sequences');

        if ($input->isInteractive()) {
            // phpcs:ignore
            if (!$io->confirm(
                "Careful, all tables and sequences will be droped.\n Do you want to continue?",
                false
            )
            ) {
                return Command::SUCCESS;
            }
        }

        $io->section('Dropping tables');

        /** @var array<int, string> $tables */
        $tables = $this->em
            ->getConnection()
            ->executeQuery("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'")
            ->fetchFirstColumn()
        ;

        foreach ($tables as $table) {
            $this->em
                ->getConnection()
                ->executeQuery("DROP TABLE \"$table\" CASCADE")
            ;
            $io->writeln("Table $table dropped");
        }

        $io->section('Dropping sequences');

        /** @var array<int, string> $sequences */
        $sequences = $this->em
            ->getConnection()
            ->executeQuery("SELECT c.relname FROM pg_class c WHERE (c.relkind = 'S')")
            ->fetchFirstColumn()
        ;

        foreach ($sequences as $sequence) {
            $this->em->getConnection()
                ->executeQuery("DROP SEQUENCE $sequence")
            ;
            $io->writeln("Sequence $sequence dropped");
        }

        $io->success('All tables and sequences have been dropped');

        return Command::SUCCESS;
    }
}
