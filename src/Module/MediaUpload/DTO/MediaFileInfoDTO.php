<?php

declare(strict_types=1);

namespace App\Module\MediaUpload\DTO;

use App\Module\MediaUpload\Enum\MediaTypeEnum;

readonly class MediaFileInfoDTO
{
    private function __construct(
        private string $fileId,
        private string $fileUniqueId,
        private MediaTypeEnum $mediaType,
        private ?int $fileSize = null,
        private ?string $filePath = null
    ) {
    }

    public static function makeDTO(
        string $fileId,
        string $fileUniqueId,
        MediaTypeEnum $mediaType,
        ?int $fileSize = null,
        ?string $filePath = null
    ): self {
        return new self($fileId, $fileUniqueId, $mediaType, $fileSize, $filePath);
    }

    public function getFileId(): string
    {
        return $this->fileId;
    }

    public function getFileUniqueId(): string
    {
        return $this->fileUniqueId;
    }

    public function getMediaType(): MediaTypeEnum
    {
        return $this->mediaType;
    }

    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'file_id' => $this->getFileId(),
            'file_unique_id' => $this->getFileUniqueId(),
            'media_type' => $this->getMediaType()->value,
            'file_size' => $this->getFileSize(),
            'file_path' => $this->getFilePath(),
        ];
    }
}
