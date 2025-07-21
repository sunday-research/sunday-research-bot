<?php

declare(strict_types=1);

namespace App\Module\BotGetUpdates\Infrastructure\Doctrine\Builder;

use App\Module\BotGetUpdates\Entity\SubscriberMessage as DomainSubscriberMessage;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Model\SubscriberMessage as DoctrineSubscriberMessage;

class SubscriberMessageBuilder
{
    public static function build(DoctrineSubscriberMessage $doctrineMessage): DomainSubscriberMessage
    {
        return new DomainSubscriberMessage(
            /** @phpstan-ignore-next-line mixed.type */
            id: $doctrineMessage->getId()?->toString(),
            /** @phpstan-ignore-next-line mixed.type */
            subscriberId: $doctrineMessage->getSubscriber()->getId()?->toString(),
            chatId: $doctrineMessage->getChatId(),
            messageId: $doctrineMessage->getMessageId(),
            messageText: $doctrineMessage->getMessageText(),
            messageDate: $doctrineMessage->getMessageDate(),
            isBotSender: $doctrineMessage->isBotSender(),
        );
    }
}
