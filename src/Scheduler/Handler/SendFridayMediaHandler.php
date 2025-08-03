<?php

declare(strict_types=1);

namespace App\Scheduler\Handler;

use App\Scheduler\Message\SendFridayMediaMessage;
use App\Module\BotCommands\DTO\SendMediaMessageDTO;
use App\Module\BotCommands\Infrastructure\Telegram\SendMessageClient;
use App\Module\MediaUpload\Service\MediaUploadService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class SendFridayMediaHandler
{
    public function __construct(
        private SendMessageClient $sendMessageClient,
        private MediaUploadService $mediaUploadService
    ) {
    }

    public function __invoke(SendFridayMediaMessage $message): void
    {
        // Если media начинается с http(s)://, это внешняя ссылка
        if (
            str_starts_with($message->getMedia(), 'http://')
            || str_starts_with($message->getMedia(), 'https://')
        ) {
            $dto = SendMediaMessageDTO::makeDTO(
                $message->getChatId(),
                $message->getMedia(),
                $message->getCaption(),
                null,
                null,
                $message->getMediaType()
            );
        } else {
            // Это локальный файл, получаем file_id через MediaUploadService
            $fileId = $this->mediaUploadService->getMediaFileId(
                $message->getMedia(),
                $message->getMediaType()
            );

            $dto = SendMediaMessageDTO::makeDTO(
                $message->getChatId(),
                $fileId,
                $message->getCaption(),
                null,
                null,
                $message->getMediaType()
            );
        }

        $this->sendMessageClient->sendMediaMessage($dto);
    }
}
