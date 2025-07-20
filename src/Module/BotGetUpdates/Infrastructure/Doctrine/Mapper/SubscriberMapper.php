<?php

declare(strict_types=1);

namespace App\Module\BotGetUpdates\Infrastructure\Doctrine\Mapper;

use App\Module\BotGetUpdates\Entity\Subscriber as DomainSubscriber;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Model\Subscriber as DoctrineSubscriber;

class SubscriberMapper
{
    public static function map(
        DomainSubscriber $domainSubscriber,
        ?DoctrineSubscriber $doctrineSubscriber = null,
    ): DoctrineSubscriber {
        $subscriber = $doctrineSubscriber ?? new DoctrineSubscriber();
        $subscriber->setTelegramUserId($domainSubscriber->getTelegramUserId());
        $subscriber->setUsername($domainSubscriber->getUsername());
        $subscriber->setFirstName($domainSubscriber->getFirstName());
        $subscriber->setLastName($domainSubscriber->getLastName());
        $subscriber->setLanguageCode($domainSubscriber->getLanguageCode());
        $subscriber->setIsPremium($domainSubscriber->isPremium());

        return $subscriber;
    }
}
