<?php

declare(strict_types=1);

namespace App\Module\BotGetUpdates\Entity;

use App\Module\BotCommands\Enum\IssueStatus;
use App\Module\BotCommands\Enum\IssuePriority;
use DateTimeInterface;

class Issue
{
    private string $id;
    private int $chatId;
    private int $messageId;
    private string $messageText;
    private IssueStatus $status;
    private IssuePriority $priority;
    private ?string $decisionComment;
    private DateTimeInterface $createdAt;
    private ?DateTimeInterface $updatedAt;
    private ?DateTimeInterface $resolvedAt;

    public function __construct(
        string $id,
        int $chatId,
        int $messageId,
        string $messageText,
        ?string $decisionComment,
        DateTimeInterface $createdAt,
        ?DateTimeInterface $updatedAt,
        ?DateTimeInterface $resolvedAt,
        IssueStatus $status = IssueStatus::NEW,
        IssuePriority $priority = IssuePriority::MEDIUM,
    ) {
        $this->id = $id;
        $this->chatId = $chatId;
        $this->messageId = $messageId;
        $this->messageText = $messageText;
        $this->status = $status;
        $this->priority = $priority;
        $this->decisionComment = $decisionComment;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->resolvedAt = $resolvedAt;
    }

    public function getId(): string
    {
        return $this->id;
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

    public function getStatus(): IssueStatus
    {
        return $this->status;
    }

    public function getPriority(): IssuePriority
    {
        return $this->priority;
    }

    public function getDecisionComment(): ?string
    {
        return $this->decisionComment;
    }
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getResolvedAt(): ?DateTimeInterface
    {
        return $this->resolvedAt;
    }
}
