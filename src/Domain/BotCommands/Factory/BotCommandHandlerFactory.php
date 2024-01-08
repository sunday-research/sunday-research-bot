<?php

declare(strict_types=1);

namespace App\Domain\BotCommands\Factory;

use App\Bundle\Share\Contract\TelegramBotApiRepositoryInterface;
use App\Domain\BotCommands\Contract\BotCommandHandlerInterface;
use App\Domain\BotCommands\Enum\BotCommandsEnum;
use App\Domain\BotCommands\Handler\BotCommandIssueHandler;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class BotCommandHandlerFactory
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private TelegramBotApiRepositoryInterface $telegramBotApiRepository,
    ) {
    }

    public function createBotCommandHandler(string $botCommandText): ?BotCommandHandlerInterface
    {
        return match ($botCommandText) {
            BotCommandsEnum::ISSUE->value => new BotCommandIssueHandler($this->messageBus, $this->telegramBotApiRepository),
            default => null,
        };
    }
}
