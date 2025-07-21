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
            /** @phpstan-ignore-next-line nullCoalesce.expr */
            username: $user->getUsername() ?? null,
            /** @phpstan-ignore-next-line nullCoalesce.expr */
            lastName: $user->getLastName() ?? null,
            /** @phpstan-ignore-next-line nullCoalesce.expr */
            languageCode: $user->getLanguageCode() ?? null,
            /** @phpstan-ignore-next-line nullCoalesce.expr */
            isPremium: $user->getIsPremium() ?? false,
        );
    }
}
