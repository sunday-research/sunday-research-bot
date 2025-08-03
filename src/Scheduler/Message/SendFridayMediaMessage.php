<?php

declare(strict_types=1);

namespace App\Scheduler\Message;

readonly class SendFridayMediaMessage
{
    public function __construct(
        private readonly string $chatId,
        private readonly string $media,
        private readonly string $caption,
        private readonly string $mediaType = 'animation'
    ) {
    }

    public function getChatId(): string
    {
        return $this->chatId;
    }

    public function getMedia(): string
    {
        return $this->media;
    }

    public function getCaption(): string
    {
        return $this->caption;
    }

    public function getMediaType(): string
    {
        return $this->mediaType;
    }
}
