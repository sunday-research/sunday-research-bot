<?php

declare(strict_types=1);

namespace App\Bundle\BotGetUpdates\Entity;

use DateTimeInterface;

class SubscriberMessage
{
    private string $id;
    private string $subscriberId;
    private int $chatId;
    private int $messageId;
    private string $messageText;
    private DateTimeInterface $messageDate;
    private bool $isBotSender;

    public function __construct(
        string $id,
        string $subscriberId,
        int $chatId,
        int $messageId,
        string $messageText,
        DateTimeInterface $messageDate,
        bool $isBotSender
    ) {
        $this->id = $id;
        $this->subscriberId = $subscriberId;
        $this->chatId = $chatId;
        $this->messageId = $messageId;
        $this->messageText = $messageText;
        $this->messageDate = $messageDate;
        $this->isBotSender = $isBotSender;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSubscriberId(): string
    {
        return $this->subscriberId;
    }

    public function getChatId(): int
    {
        return $this->chatId;
    }

    public function getMessageId(): int
    {
        return $this->messageId;
    }

    public function getMessageText(): string
    {
        return $this->messageText;
    }

    public function getMessageDate(): DateTimeInterface
    {
        return $this->messageDate;
    }

    public function isBotSender(): bool
    {
        return $this->isBotSender;
    }
}
