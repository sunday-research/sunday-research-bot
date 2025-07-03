<?php

declare(strict_types=1);

namespace App\Bundle\BotGetUpdates\Infrastructure\Doctrine\Builder;

use App\Bundle\BotGetUpdates\Entity\SubscriberMessage as DomainSubscriberMessage;
use App\Bundle\BotGetUpdates\Infrastructure\Doctrine\Model\SubscriberMessage as DoctrineSubscriberMessage;

class SubscriberMessageBuilder
{
    public static function build(DoctrineSubscriberMessage $doctrineMessage): DomainSubscriberMessage
    {
        return new DomainSubscriberMessage(
            id: $doctrineMessage->getId()?->toString(),
            subscriberId: $doctrineMessage->getSubscriber()->getId()?->toString(),
            chatId: $doctrineMessage->getChatId(),
            messageId: $doctrineMessage->getMessageId(),
            messageText: $doctrineMessage->getMessageText(),
            messageDate: $doctrineMessage->getMessageDate(),
            isBotSender: $doctrineMessage->isBotSender(),
        );
    }
}
