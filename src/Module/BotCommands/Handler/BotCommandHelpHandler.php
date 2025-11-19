<?php

declare(strict_types=1);

namespace App\Module\BotCommands\Handler;

use App\Module\BotCommands\Contract\BotCommandHandlerInterface;
use App\Module\BotCommands\DTO\SendTextMessageDTO;
use App\Module\BotCommands\Infrastructure\Telegram\SendMessageClient;
use App\Module\BotCommands\Message\BotCommandHelpMessage;
use Longman\TelegramBot\Entities\Update;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class BotCommandHelpHandler implements BotCommandHandlerInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {
    }

    public function handle(Update $update): void
    {
        $this->messageBus->dispatch(new BotCommandHelpMessage($update));
    }
}
