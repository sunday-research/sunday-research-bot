<?php

declare(strict_types=1);

namespace App\Domain\BotCommands\Handler;

use App\Bundle\Share\Contract\TelegramBotApiRepositoryInterface;
use App\Bundle\Share\DTO\SendTextMessageDTO;
use App\Domain\BotCommands\Contract\BotCommandHandlerInterface;
use App\Domain\BotCommands\Message\BotCommandIssueMessage;
use Longman\TelegramBot\Entities\Update;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class BotCommandIssueHandler implements BotCommandHandlerInterface
{
    /**
     * @todo: выглядит неправильно, что используется репозиторий напрямую. Но я пока не уверен, где должен быть этот код
     */
    public function __construct(
        private MessageBusInterface $messageBus,
        private TelegramBotApiRepositoryInterface $telegramBotApiRepository,
    ) {
    }

    public function handle(Update $update): void
    {
        $this->messageBus->dispatch(new BotCommandIssueMessage($update));
        $this->telegramBotApiRepository->sendTextMessage(
            SendTextMessageDTO::makeDTO(
                (string)$update->getMessage()->getChat()->getId(),
                'Ваш запрос принят!'
            )
        );
    }
}
