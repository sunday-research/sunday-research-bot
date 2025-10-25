<?php

declare(strict_types=1);

namespace App\Module\MediaUpload\Infrastructure\Redis\Repository;

use App\Module\MediaUpload\DTO\MediaFileInfoDTO;
use App\Module\MediaUpload\Enum\MediaTypeEnum;
use App\Module\MediaUpload\ValueObject\MediaFileHashVO;
use Predis\ClientInterface;

final readonly class MediaUploadCacheRepository
{
    private const CACHE_KEY_PREFIX = 'media_upload_cache';
    private const CACHE_TTL_IN_SECONDS = 86400; // 24 hours

    public function __construct(private ClientInterface $client)
    {
    }

    public function isMediaFileCached(MediaFileHashVO $fileHash): bool
    {
        $cacheKey = $this->getCacheKey($fileHash);
        return (bool)$this->client->exists($cacheKey);
    }

    public function getMediaFileInfo(MediaFileHashVO $fileHash): ?MediaFileInfoDTO
    {
        $cacheKey = $this->getCacheKey($fileHash);

        /**
         * @var array{}|array{file_id: string, file_unique_id: string, media_type: string, file_size: int, file_path: string}
         */
        $fileInfo = $this->client->hgetall($cacheKey);

        if (empty($fileInfo)) {
            return null;
        }

        return MediaFileInfoDTO::makeDTO(
            fileId: $fileInfo['file_id'],
            fileUniqueId: $fileInfo['file_unique_id'],
            mediaType: MediaTypeEnum::from($fileInfo['media_type']),
            fileSize: $fileInfo['file_size'],
            filePath: $fileInfo['file_path']
        );
    }

    public function setMediaFileInfo(MediaFileHashVO $fileHash, MediaFileInfoDTO $fileInfo, ?int $ttlInSeconds = null): bool
    {
        if (null === $ttlInSeconds) {
            $ttlInSeconds = self::CACHE_TTL_IN_SECONDS;
        }

        $cacheKey = $this->getCacheKey($fileHash);

        /**
         * @var array{file_id: string, file_unique_id: string, media_type: string, file_size: int, file_path: string}
         */
        $data = $fileInfo->toArray();

        // Используем NX (только если ключ не существует)
        $result = $this->client->hsetnx($cacheKey, 'file_id', $data['file_id']);
        if ($result) {
            // Если ключ был создан, добавляем остальные поля и устанавливаем TTL
            $this->client->multi();
            $this->client->hset($cacheKey, 'file_unique_id', $data['file_unique_id']);
            $this->client->hset($cacheKey, 'media_type', $data['media_type']);
            $this->client->hset($cacheKey, 'file_size', (string)$data['file_size']);
            $this->client->hset($cacheKey, 'file_path', $data['file_path']);
            $this->client->expire($cacheKey, $ttlInSeconds);
            $this->client->exec();

            return true;
        }

        return false;
    }

    public function deleteMediaFileInfo(MediaFileHashVO $fileHash): void
    {
        $cacheKey = $this->getCacheKey($fileHash);
        $this->client->del($cacheKey);
    }

    private function getCacheKey(MediaFileHashVO $fileHash): string
    {
        return sprintf('%s_%s', self::CACHE_KEY_PREFIX, $fileHash->getHash());
    }
}
