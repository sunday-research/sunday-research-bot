<?php

declare(strict_types=1);

namespace App\Module\MediaUpload\DTO;

use App\Module\MediaUpload\Enum\MediaTypeEnum;

readonly class UploadMediaDTO
{
    private function __construct(
        private string $filePath,
        private MediaTypeEnum $mediaType,
        private ?string $caption = null,
        private ?string $parseMode = null
    ) {
    }

    public static function makeDTO(
        string $filePath,
        MediaTypeEnum $mediaType,
        ?string $caption = null,
        ?string $parseMode = null
    ): self {
        return new self($filePath, $mediaType, $caption, $parseMode);
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function getMediaType(): MediaTypeEnum
    {
        return $this->mediaType;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function getParseMode(): ?string
    {
        return $this->parseMode;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'file_path' => $this->getFilePath(),
            'media_type' => $this->getMediaType()->value,
            'caption' => $this->getCaption(),
            'parse_mode' => $this->getParseMode(),
        ];
    }
}
