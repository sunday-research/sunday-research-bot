<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\BotGetUpdates\Manager\BotGetUpdatesManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\SignalableCommandInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand(
    name: 'app:bot-update-listener',
    description: 'Команда для получения входящих обновлений для бота методом long polling',
)]
final class BotUpdateListenerCommand extends Command implements SignalableCommandInterface
{
    private bool $wasStop = false;

    public function __construct(
        private readonly BotGetUpdatesManager $botGetUpdatesManager,
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        while (true) {
            if ($this->wasStop) {
                return Command::SUCCESS;
            }
            try {
                $this->botGetUpdatesManager->run();
            } catch (Throwable $e) {
                $this->logger->error(
                    sprintf('Unhandled error while process getUpdates: `%s`', $e->getMessage()),
                    [
                        'trace' => $e->getTraceAsString()
                    ]
                );
            }
        }
    }

    public function getSubscribedSignals(): array
    {
        return [SIGINT, SIGTERM, SIGQUIT];
    }

    public function handleSignal(int $signal, false|int $previousExitCode = 0): int|false
    {
        $this->wasStop = true;
        $this->logger->info('Graceful shutdown handled', ['signal' => $signal, 'wasStop' => $this->wasStop]);
        return false; // continue normal execution
    }
}
