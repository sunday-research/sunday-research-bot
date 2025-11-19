<?php

declare(strict_types=1);

namespace App\Module\BotCommands\Factory;

use App\Module\BotCommands\Contract\BotCommandHandlerInterface;
use App\Module\BotCommands\Enum\BotCommandsEnum;
use App\Module\BotCommands\Handler\BotCommandHelpHandler;
use App\Module\BotCommands\Handler\BotCommandIssueHandler;
use App\Module\BotCommands\Infrastructure\Telegram\SendMessageClient;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @todo: рассмотреть возможность использования pipeline вместо фабрики
 */
final readonly class BotCommandHandlerFactory
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private SendMessageClient $telegramBotApiClient,
    ) {
    }

    public function createBotCommandHandler(string $botCommandText): ?BotCommandHandlerInterface
    {
        return match ($botCommandText) {
            BotCommandsEnum::ISSUE->value => new BotCommandIssueHandler($this->messageBus, $this->telegramBotApiClient),
            BotCommandsEnum::HELP->value => new BotCommandHelpHandler($this->messageBus),
            default => null,
        };
    }
}
