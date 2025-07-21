<?php

declare(strict_types=1);

namespace App\Module\BotCommands\ValueObject;

use Longman\TelegramBot\Entities\MessageEntity;

readonly class ReplyParameters
{
    public function __construct(
        private int $messageId,
        private ?string $chatId = null,
        private bool $allowSendingWithoutReply = true,
        private ?string $quote = null,
        private ?string $quoteParseMode = null,
        /**
         * @var MessageEntity[]
         */
        private array $quoteEntities = [],
        private ?int $quotePosition = null,
    ) {
    }

    public function getMessageId(): int
    {
        return $this->messageId;
    }

    public function getChatId(): ?string
    {
        return $this->chatId;
    }

    public function isAllowSendingWithoutReply(): bool
    {
        return $this->allowSendingWithoutReply;
    }

    public function getQuote(): ?string
    {
        return $this->quote;
    }

    public function getQuoteParseMode(): ?string
    {
        return $this->quoteParseMode;
    }

    /**
     * @return MessageEntity[]
     */
    public function getQuoteEntities(): array
    {
        return $this->quoteEntities;
    }

    public function getQuotePosition(): ?int
    {
        return $this->quotePosition;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'messageId' => $this->getMessageId(),
            'chatId' => $this->getChatId(),
            'allowSendingWithoutReply' => $this->isAllowSendingWithoutReply(),
            'quote' => $this->getQuote(),
            'quoteParseMode' => $this->getQuoteParseMode(),
            'quoteEntities' => $this->getQuoteEntities(),
            'quotePosition' => $this->getQuotePosition(),
        ];
    }
}
