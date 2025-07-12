<?php

declare(strict_types=1);

namespace App\Module\BotCommands\Handler;

use App\Module\BotCommands\Contract\BotCommandHandlerInterface;
use App\Module\BotCommands\DTO\SendTextMessageDTO;
use App\Module\BotCommands\Infrastructure\Telegram\SendMessageClient;
use App\Module\BotCommands\Message\BotCommandIssueMessage;
use Longman\TelegramBot\Entities\Update;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class BotCommandIssueHandler implements BotCommandHandlerInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private SendMessageClient $telegramBotApiClient,
    ) {
    }

    public function handle(Update $update): void
    {
        $this->messageBus->dispatch(new BotCommandIssueMessage($update));
        $this->telegramBotApiClient->sendTextMessage(
            SendTextMessageDTO::makeDTO(
                (string)$update->getMessage()->getChat()->getId(),
                'Ваш запрос принят!'
            )
        );
    }
}
