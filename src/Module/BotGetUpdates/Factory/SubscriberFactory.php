<?php

namespace App\Module\BotGetUpdates\Factory;

use App\Module\BotGetUpdates\Entity\Subscriber;
use Longman\TelegramBot\Entities\User;
use Ramsey\Uuid\Uuid;

class SubscriberFactory
{
    public static function fromTelegramUser(User $user): Subscriber
    {
        return new Subscriber(
            id: Uuid::uuid7()->toString(),
            telegramUserId: $user->getId(),
            firstName: $user->getFirstName(),
            username: $user->getUsername() ?? null,
            lastName: $user->getLastName() ?? null,
            languageCode: $user->getLanguageCode() ?? null,
            isPremium: $user->getIsPremium() ?? false,
        );
    }
}
