<?php

declare(strict_types=1);

namespace App\Bundle\BotGetUpdates\Entity;

class Subscriber
{
    private string $id;
    private int $telegramUserId;
    private ?string $username;
    private string $firstName;
    private ?string $lastName;
    private ?string $languageCode;
    private bool $isPremium;

    public function __construct(
        string $id,
        int $telegramUserId,
        string $firstName,
        ?string $username = null,
        ?string $lastName = null,
        ?string $languageCode = null,
        bool $isPremium = false
    ) {
        $this->id = $id;
        $this->telegramUserId = $telegramUserId;
        $this->firstName = $firstName;
        $this->username = $username;
        $this->lastName = $lastName;
        $this->languageCode = $languageCode;
        $this->isPremium = $isPremium;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTelegramUserId(): int
    {
        return $this->telegramUserId;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getLanguageCode(): ?string
    {
        return $this->languageCode;
    }

    public function isPremium(): bool
    {
        return $this->isPremium;
    }
}
