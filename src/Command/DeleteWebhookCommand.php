<?php

declare(strict_types=1);

namespace App\Command;

use Longman\TelegramBot\Telegram;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand(
    name: 'app:delete-webhook',
    description: 'Удаляет вебхук для Telegram-бота, если нужно использовать long polling для получения обновлений (getUpdates)',
)]
class DeleteWebhookCommand extends Command
{
    public function __construct(private readonly Telegram $telegram)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $result = $this->telegram->deleteWebhook();
            if (!$result->isOk()) {
                throw new RuntimeException('Failed to delete webhook: ' . $result->getDescription());
            }
        } catch (Throwable $e) {
            $io->error('Error deleting webhook: ' . $e->getMessage());

            return Command::FAILURE;
        }

        $io->success('Webhook removal is complete.');

        return Command::SUCCESS;
    }
}
