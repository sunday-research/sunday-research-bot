<?php

declare(strict_types=1);

namespace App\Module\BotCommands\DTO;

use App\Module\BotCommands\ValueObject\LinkPreviewOptions;
use App\Module\BotCommands\ValueObject\ReplyParameters;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\MessageEntity;

readonly class SendTextMessageDTO
{
    private function __construct(
        private string $chatId,
        private string $text,
        private ?int $messageThreadId = null,
        private ?string $parseMode = null,
        /**
         * @var MessageEntity[]
         */
        private array $entities = [],
        private ?LinkPreviewOptions $linkPreviewOptions = null,
        private bool $disableNotification = false,
        private bool $protectContent = false,
        private ?ReplyParameters $replyParameters = null,
        private ?Keyboard $replyMarkup = null,
    ) {
    }

    /**
     * @param MessageEntity[] $entities
     */
    public static function makeDTO(
        string $chatId,
        string $text,
        ?int $messageThreadId = null,
        ?string $parseMode = null,
        array $entities = [],
        ?LinkPreviewOptions $linkPreviewOptions = null,
        bool $disableNotification = false,
        bool $protectContent = false,
        ?ReplyParameters $replyParameters = null,
        ?Keyboard $replyMarkup = null
    ): self {
        return new self(
            $chatId,
            $text,
            $messageThreadId,
            $parseMode,
            $entities,
            $linkPreviewOptions,
            $disableNotification,
            $protectContent,
            $replyParameters,
            $replyMarkup
        );
    }

    public function getChatId(): string
    {
        return $this->chatId;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getMessageThreadId(): ?int
    {
        return $this->messageThreadId;
    }

    public function getParseMode(): ?string
    {
        return $this->parseMode;
    }

    /**
     * @return MessageEntity[]
     */
    public function getEntities(): array
    {
        return $this->entities;
    }

    public function getLinkPreviewOptions(): ?LinkPreviewOptions
    {
        return $this->linkPreviewOptions;
    }

    public function isDisableNotification(): bool
    {
        return $this->disableNotification;
    }

    public function isProtectContent(): bool
    {
        return $this->protectContent;
    }

    public function getReplyParameters(): ?ReplyParameters
    {
        return $this->replyParameters;
    }

    public function getReplyMarkup(): ?Keyboard
    {
        return $this->replyMarkup;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'chatId' => $this->getChatId(),
            'text' => $this->getText(),
            'messageThreadId' => $this->getMessageThreadId(),
            'parseMode' => $this->getParseMode(),
            'entities' => $this->getEntities(),
            'linkPreviewOptions' => $this->getLinkPreviewOptions()?->toArray(),
            'disableNotification' => $this->isDisableNotification(),
            'protectContent' => $this->isProtectContent(),
            'replyParameters' => $this->getReplyParameters()?->toArray(),
            'replyMarkup' => $this->getReplyMarkup(),
        ];
    }
}
