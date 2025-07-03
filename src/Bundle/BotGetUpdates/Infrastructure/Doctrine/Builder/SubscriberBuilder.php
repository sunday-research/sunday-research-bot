<?php

declare(strict_types=1);

namespace App\Bundle\BotGetUpdates\Infrastructure\Doctrine\Builder;

use App\Bundle\BotGetUpdates\Entity\Subscriber as DomainSubscriber;
use App\Bundle\BotGetUpdates\Infrastructure\Doctrine\Model\Subscriber as DoctrineSubscriber;

class SubscriberBuilder
{
    public static function build(DoctrineSubscriber $doctrineSubscriber): DomainSubscriber
    {
        return new DomainSubscriber(
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
