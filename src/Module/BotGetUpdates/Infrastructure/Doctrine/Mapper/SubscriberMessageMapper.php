<?php

declare(strict_types=1);

namespace App\Module\BotGetUpdates\Infrastructure\Doctrine\Mapper;

use App\Module\BotGetUpdates\Entity\SubscriberMessage as DomainSubscriberMessage;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Model\Subscriber as DoctrineSubscriber;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Model\SubscriberMessage as DoctrineSubscriberMessage;

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
