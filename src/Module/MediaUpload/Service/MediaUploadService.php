<?php

declare(strict_types=1);

namespace App\Module\MediaUpload\Service;

use App\Module\MediaUpload\DTO\MediaFileInfoDTO;
use App\Module\MediaUpload\DTO\UploadMediaDTO;
use App\Module\MediaUpload\Infrastructure\Redis\Repository\MediaUploadCacheRepository;
use App\Module\MediaUpload\Infrastructure\Telegram\MediaUploadClient;
use App\Module\MediaUpload\ValueObject\MediaFileHash;

final readonly class MediaUploadService
{
    public function __construct(
        private MediaUploadClient $mediaUploadClient,
        private MediaUploadCacheRepository $cacheRepository
    ) {
    }

    /**
     * Загружает медиа-файл в Telegram или возвращает кэшированную информацию
     */
    public function uploadMediaWithCache(UploadMediaDTO $uploadMediaDTO): MediaFileInfoDTO
    {
        $fileHash = MediaFileHash::fromFilePath($uploadMediaDTO->getFilePath());

        // Проверяем кэш
        if ($this->cacheRepository->isMediaFileCached($fileHash)) {
            $cachedInfo = $this->cacheRepository->getMediaFileInfo($fileHash);
            if ($cachedInfo !== null) {
                return $cachedInfo;
            }
        }

        // Загружаем файл в Telegram
        $fileInfo = $this->mediaUploadClient->uploadMedia($uploadMediaDTO);

        // Кэшируем информацию (используем NX для избежания коллизий)
        $this->cacheRepository->setMediaFileInfo($fileHash, $fileInfo);

        return $fileInfo;
    }

    /**
     * Получает file_id для медиа-файла (из кэша или загружает)
     */
    public function getMediaFileId(string $filePath, string $mediaType): string
    {
        $uploadDTO = UploadMediaDTO::makeDTO(
            filePath: $filePath,
            mediaType: \App\Module\MediaUpload\Enum\MediaTypeEnum::from($mediaType)
        );

        $fileInfo = $this->uploadMediaWithCache($uploadDTO);
        return $fileInfo->getFileId();
    }

    /**
     * Очищает кэш для конкретного файла
     */
    public function clearCache(string $filePath): void
    {
        $fileHash = MediaFileHash::fromFilePath($filePath);
        $this->cacheRepository->deleteMediaFileInfo($fileHash);
    }
} 