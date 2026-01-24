<?php

declare(strict_types=1);

namespace App\Module\BotGetUpdates\Infrastructure\Doctrine\Model;

use App\Module\BotCommands\Enum\IssuePriority;
use App\Module\BotCommands\Enum\IssueStatus;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Repository\IssueRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: IssueRepository::class)]
#[ORM\Table(name: "subscriber_issue")]
#[ORM\Index(name: "idx_status_priority", columns: ["status", "priority"])]
#[ORM\Index(name: "idx_created_at", columns: ["created_at"])]
class Issue
{
    #[ORM\Id]
    #[ORM\Column(name: "id", type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: Subscriber::class)]
    #[ORM\JoinColumn(name: "subscriber_id", referencedColumnName: "id", nullable: true)]
    private ?Subscriber $subscriber = null;

    #[ORM\Column(name: "chat_id", type: "bigint", nullable: false)]
    private int $chatId;

    #[ORM\Column(name: "message_id", type: "bigint", nullable: false)]
    private int $messageId;

    #[ORM\Column(name: "message_text", type: "text", nullable: false)]
    private string $messageText;

    #[ORM\Column(name: "status", type: "string", length: 24, nullable: false, options: ["default" => "new"])]
    private string $status;

    #[ORM\Column(name: "priority", type: "string", length: 24, nullable: false, options: ["default" => "medium"])]
    private string $priority;

    #[ORM\Column(name: "decision_comment", type: "text", nullable: true)]
    private ?string $decisionComment = null;

    #[ORM\Column(name: "created_at", type: "datetime_immutable", nullable: false)]
    private DateTimeInterface $createdAt;

    #[ORM\Column(name: "updated_at", type: "datetime_immutable", nullable: true)]
    private ?DateTimeInterface $updatedAt = null;

    #[ORM\Column(name: "resolved_at", type: "datetime_immutable", nullable: true)]
    private ?DateTimeInterface $resolvedAt = null;

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new \DateTimeImmutable();

        if (!isset($this->status)) {
            $this->status = IssueStatus::NEW->value;
        }
        if (!isset($this->priority)) {
            $this->priority = IssuePriority::MEDIUM->value;
        }
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getSubscriber(): ?Subscriber
    {
        return $this->subscriber;
    }

    public function setSubscriber(?Subscriber $subscriber): self
    {
        $this->subscriber = $subscriber;

        return $this;
    }

    public function getChatId(): int
    {
        return $this->chatId;
    }

    public function setChatId(int $chatId): self
    {
        $this->chatId = $chatId;
        return $this;
    }

    public function getMessageId(): int
    {
        return $this->messageId;
    }

    public function setMessageId(int $messageId): self
    {
        $this->messageId = $messageId;
        return $this;
    }

    public function getMessageText(): string
    {
        return $this->messageText;
    }

    public function setMessageText(string $messageText): self
    {
        $this->messageText = $messageText;
        return $this;
    }

    public function getStatus(): IssueStatus
    {
        return IssueStatus::from($this->status);
    }

    public function setStatus(IssueStatus $status): self
    {
        $this->status = $status->value;
        return $this;
    }

    public function getPriority(): IssuePriority
    {
        return IssuePriority::from($this->priority);
    }

    public function setPriority(IssuePriority $priority): self
    {
        $this->priority = $priority->value;
        return $this;
    }

    public function getDecisionComment(): ?string
    {
        return $this->decisionComment;
    }

    public function setDecisionComment(?string $decisionComment): self
    {
        $this->decisionComment = $decisionComment;
        return $this;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getResolvedAt(): ?DateTimeInterface
    {
        return $this->resolvedAt;
    }

    public function setResolvedAt(?DateTimeInterface $resolvedAt): self
    {
        $this->resolvedAt = $resolvedAt;
        return $this;
    }
}
