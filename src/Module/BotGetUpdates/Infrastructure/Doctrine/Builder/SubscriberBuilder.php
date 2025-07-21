<?php

declare(strict_types=1);

namespace App\Module\BotGetUpdates\Infrastructure\Doctrine\Builder;

use App\Module\BotGetUpdates\Entity\Subscriber as DomainSubscriber;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Model\Subscriber as DoctrineSubscriber;

class SubscriberBuilder
{
    public static function build(DoctrineSubscriber $doctrineSubscriber): DomainSubscriber
    {
        return new DomainSubscriber(
            /** @phpstan-ignore-next-line mixed.type */
            id: $doctrineSubscriber->getId()?->toString(),
            telegramUserId: $doctrineSubscriber->getTelegramUserId(),
            firstName: $doctrineSubscriber->getFirstName(),
            username: $doctrineSubscriber->getUsername(),
            lastName: $doctrineSubscriber->getLastName(),
            languageCode: $doctrineSubscriber->getLanguageCode(),
            isPremium: $doctrineSubscriber->isPremium(),
        );
    }
}
