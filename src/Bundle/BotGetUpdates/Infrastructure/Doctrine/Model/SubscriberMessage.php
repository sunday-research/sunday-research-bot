<?php

namespace App\Bundle\BotGetUpdates\Infrastructure\Doctrine\Model;

use App\Bundle\BotGetUpdates\Infrastructure\Doctrine\Repository\SubscriberMessageRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;

#[ORM\Entity(repositoryClass: SubscriberMessageRepository::class)]
#[ORM\Table(
    name: "subscriber_messages",
    indexes: [
        new ORM\Index(name: "idx_subscriber_id", columns: ["subscriber_id"]),
        new ORM\Index(name: "idx_chat_message", columns: ["chat_id", "message_id"])
    ]
)]
class SubscriberMessage
{
    #[ORM\Id]
    #[ORM\Column(name: "id", type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?UuidInterface $id = null;

    #[ORM\ManyToOne(targetEntity: Subscriber::class)]
    #[ORM\JoinColumn(name: "subscriber_id", referencedColumnName: "id", nullable: false, onDelete: "RESTRICT")]
    private Subscriber $subscriber;

    #[ORM\Column(name: "chat_id", type: "bigint", nullable: false)]
    private int $chatId;

    #[ORM\Column(name: "message_id", type: "bigint", nullable: false)]
    private int $messageId;

    #[ORM\Column(name: "message_text", type: "text", nullable: false)]
    private string $messageText;

    #[ORM\Column(name: "message_date", type: "datetime", nullable: false)]
    private DateTimeInterface $messageDate;

    #[ORM\Column(name: "is_bot_sender", type: "boolean", nullable: false)]
    private bool $isBotSender = false;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getSubscriber(): Subscriber
    {
        return $this->subscriber;
    }

    public function setSubscriber(Subscriber $subscriber): self
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

    public function getMessageDate(): DateTimeInterface
    {
        return $this->messageDate;
    }

    public function setMessageDate(DateTimeInterface $messageDate): self
    {
        $this->messageDate = $messageDate;

        return $this;
    }

    public function isBotSender(): bool
    {
        return $this->isBotSender;
    }

    public function setIsBotSender(bool $isBotSender): self
    {
        $this->isBotSender = $isBotSender;

        return $this;
    }
}
