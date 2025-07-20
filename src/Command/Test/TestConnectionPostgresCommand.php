<?php

declare(strict_types=1);

namespace App\Command\Test;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand(
    name: 'test:connection-postgres',
    description: 'Тестирует соединение с PostgreSQL-сервером',
)]
final class TestConnectionPostgresCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $connect = $this->entityManager->getConnection();
            $result = $connect->executeQuery('SELECT NOW()');
            $io->success("PostgreSQL is running, current time: {$result->fetchOne()}");
            $connect->close();

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $io->error("Something went wrong.. Error message: {$e->getMessage()}");

            return Command::FAILURE;
        }
    }
}
