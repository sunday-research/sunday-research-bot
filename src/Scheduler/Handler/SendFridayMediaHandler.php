<?php

declare(strict_types=1);

namespace App\Scheduler\Handler;

use App\Scheduler\Message\SendFridayMediaMessage;
use App\Module\BotCommands\DTO\SendMediaMessageDTO;
use App\Module\BotCommands\Infrastructure\Telegram\SendMessageClient;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class SendFridayMediaHandler
{
    public function __construct(private SendMessageClient $sendMessageClient)
    {
    }

    public function __invoke(SendFridayMediaMessage $message): void
    {
        $dto = SendMediaMessageDTO::makeDTO(
            $message->getChatId(),
            $message->getMedia(),
            $message->getCaption(),
            null,
            null,
            $message->getMediaType()
        );
        $this->sendMessageClient->sendMediaMessage($dto);
    }
}
