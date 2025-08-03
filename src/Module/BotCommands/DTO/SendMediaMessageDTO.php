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
    ) {
    }

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

    public function isFileId(): bool
    {
        // Проверяем, является ли media внешней ссылкой
        if (str_starts_with($this->media, 'http://') || str_starts_with($this->media, 'https://')) {
            return false; // Внешняя ссылка - не file_id
        }

        // Проверяем, является ли media file_id (обычно начинается с определенных префиксов)
        // Telegram file_id обычно содержит специальные символы и имеет определенную структуру
        if (preg_match('/^[A-Za-z0-9_-]{20,}$/', $this->media)) {
            return true; // Похоже на file_id
        }

        // Если это не внешняя ссылка и не похоже на file_id, то это путь к файлу
        return false;
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
