<?php

declare(strict_types=1);

namespace App\Command\Test;

use Predis\Client;
use Predis\Response\Status;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand(
    name: 'test:connection-redis',
    description: 'Command for testing connection to Redis server',
)]
final class TestConnectionRedisCommand extends Command
{
    public function __construct(private readonly Client $client)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->client->connect();
            /** @var Status $status */
            $status = $this->client->ping();
            $payload = $status->getPayload();

            if ($payload === 'PONG') {
                $io->success('Redis is running: we have PONG answer!');
                return Command::SUCCESS;
            } else {
                $io->error("Something went wrong.. Response: {$payload}");
                return Command::FAILURE;
            }
        } catch (Throwable $e) {
            $io->error("Something went wrong.. Error message: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}
