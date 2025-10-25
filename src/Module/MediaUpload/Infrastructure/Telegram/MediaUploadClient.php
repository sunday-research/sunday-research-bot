<?php

declare(strict_types=1);

namespace App\Module\MediaUpload\Infrastructure\Telegram;

use App\Module\MediaUpload\DTO\MediaFileInfoDTO;
use App\Module\MediaUpload\DTO\UploadMediaDTO;
use App\Module\MediaUpload\Exception\MediaFileNotFoundException;
use App\Module\MediaUpload\Exception\MediaUploadFailedException;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Entities\ServerResponse;

final readonly class MediaUploadClient
{
    /**
     * @note Свойство $telegram используется неявно статичным классом \Longman\TelegramBot\Request
     * @phpstan-ignore-next-line property.onlyWritten
     */
    public function __construct(private Telegram $telegram)
    {
    }

    /**
     * @throws MediaUploadFailedException
     * @throws MediaFileNotFoundException
     */
    public function uploadMedia(UploadMediaDTO $uploadMediaDTO): MediaFileInfoDTO
    {
        $filePath = $uploadMediaDTO->getFilePath();

        if (!file_exists($filePath)) {
            throw new MediaFileNotFoundException($filePath);
        }

        $mediaType = $uploadMediaDTO->getMediaType();
        $method = $mediaType->getTelegramMethod();
        $fileField = $mediaType->getFileField();

        $data = [
            'chat_id' => '1', // Используем тестовый chat_id для загрузки
            $fileField => Request::encodeFile($filePath),
        ];

        if ($uploadMediaDTO->getCaption()) {
            $data['caption'] = $uploadMediaDTO->getCaption();
        }

        if ($uploadMediaDTO->getParseMode()) {
            $data['parse_mode'] = $uploadMediaDTO->getParseMode();
        }

        /**
         * @var ServerResponse $response
         */
        $response = Request::{$method}($data);

        if (!$response->isOk()) {
            throw new MediaUploadFailedException(
                sprintf('Failed to upload media, error: `%s`', $response->getDescription())
            );
        }

        $result = $response->getResult();
        /** @var \Longman\TelegramBot\Entities\File $file */
        $file = $result->get($fileField); // @phpstan-ignore-line

        return MediaFileInfoDTO::makeDTO(
            fileId: $file->getFileId(),
            fileUniqueId: $file->getFileUniqueId(),
            mediaType: $mediaType,
            fileSize: $file->getFileSize(),
            filePath: $file->getFilePath()
        );
    }
}
