<?php

declare(strict_types=1);

namespace App\Module\BotCommands\DTO;

use Longman\TelegramBot\Entities\Keyboard;

readonly class SendMediaMessageDTO
{
    private function __construct(
        private string $chatId,
        private string $media,
        private ?string $caption = null,
        private ?string $parseMode = null,
        private ?Keyboard $replyMarkup = null,
        private string $mediaType = 'photo' // 'photo', 'animation', or 'document'
    ) {}

    public static function makeDTO(
        string $chatId,
        string $media,
        ?string $caption = null,
        ?string $parseMode = null,
        ?Keyboard $replyMarkup = null,
        string $mediaType = 'photo'
    ): self {
        return new self($chatId, $media, $caption, $parseMode, $replyMarkup, $mediaType);
    }

    public function getChatId(): string
    {
        return $this->chatId;
    }

    public function getMedia(): string
    {
        return $this->media;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function getParseMode(): ?string
    {
        return $this->parseMode;
    }

    public function getReplyMarkup(): ?Keyboard
    {
        return $this->replyMarkup;
    }

    public function getMediaType(): string
    {
        return $this->mediaType;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'chat_id' => $this->getChatId(),
            $this->getMediaType() => $this->getMedia(),
            'caption' => $this->getCaption(),
            'parse_mode' => $this->getParseMode(),
            'reply_markup' => $this->getReplyMarkup(),
        ];
    }
}
