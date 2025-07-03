<?php

declare(strict_types=1);

namespace App\Bundle\BotGetUpdates\Infrastructure\Doctrine\Mapper;

use App\Bundle\BotGetUpdates\Entity\SubscriberMessage as DomainSubscriberMessage;
use App\Bundle\BotGetUpdates\Infrastructure\Doctrine\Model\SubscriberMessage as DoctrineSubscriberMessage;
use App\Bundle\BotGetUpdates\Infrastructure\Doctrine\Model\Subscriber as DoctrineSubscriber;

class SubscriberMessageMapper
{
    public static function map(
        DomainSubscriberMessage $domainSubscriberMessage,
        DoctrineSubscriber $doctrineSubscriber,
        ?DoctrineSubscriberMessage $doctrineSubscriberMessage = null,
    ): DoctrineSubscriberMessage {
        $subscriberMessage = $doctrineSubscriberMessage ?? new DoctrineSubscriberMessage();
        $subscriberMessage
            ->setSubscriber($doctrineSubscriber)
            ->setChatId($domainSubscriberMessage->getChatId())
            ->setMessageId($domainSubscriberMessage->getMessageId())
            ->setMessageText($domainSubscriberMessage->getMessageText())
            ->setMessageDate($domainSubscriberMessage->getMessageDate())
            ->setIsBotSender($domainSubscriberMessage->isBotSender())
        ;

        return $subscriberMessage;
    }
}
