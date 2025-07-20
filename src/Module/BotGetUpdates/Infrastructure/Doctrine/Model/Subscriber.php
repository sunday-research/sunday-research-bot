<?php

declare(strict_types=1);

namespace App\Module\BotGetUpdates\Infrastructure\Doctrine\Model;

use App\Module\BotGetUpdates\Infrastructure\Doctrine\Repository\SubscriberRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Table(name: "subscriber")]
#[ORM\Entity(repositoryClass: SubscriberRepository::class)]
class Subscriber
{
    #[ORM\Id]
    #[ORM\Column(name: "id", type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?UuidInterface $id = null;

    #[ORM\Column(name: "telegram_user_id", type: "bigint", unique: true)]
    private int $telegramUserId;

    #[ORM\Column(name: "username", type: "string", length: 255, nullable: true)]
    private ?string $username = null;

    #[ORM\Column(name: "first_name", type: "string", length: 255)]
    private string $firstName;

    #[ORM\Column(name: "last_name", type: "string", length: 255, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(name: "language_code", type: "string", length: 10, nullable: true)]
    private ?string $languageCode = null;

    #[ORM\Column(name: "is_premium", type: "boolean")]
    private bool $isPremium = false;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getTelegramUserId(): int
    {
        return $this->telegramUserId;
    }

    public function setTelegramUserId(int $telegramUserId): self
    {
        $this->telegramUserId = $telegramUserId;

        return $this;
    }


    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getLanguageCode(): ?string
    {
        return $this->languageCode;
    }

    public function setLanguageCode(?string $languageCode): self
    {
        $this->languageCode = $languageCode;
        return $this;
    }

    public function isPremium(): bool
    {
        return $this->isPremium;
    }

    public function setIsPremium(bool $isPremium): self
    {
        $this->isPremium = $isPremium;
        return $this;
    }
}
