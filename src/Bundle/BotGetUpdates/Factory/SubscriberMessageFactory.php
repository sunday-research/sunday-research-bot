<?php

declare(strict_types=1);

namespace App\Bundle\BotGetUpdates\Factory;

use App\Bundle\BotGetUpdates\Entity\Subscriber;
use App\Bundle\BotGetUpdates\Entity\SubscriberMessage;
use DateTimeImmutable;
use Longman\TelegramBot\Entities\Message;
use Ramsey\Uuid\Uuid;

class SubscriberMessageFactory
{
    public static function fromTelegramMessage(Message $message, Subscriber $subscriber): SubscriberMessage
    {
        return new SubscriberMessage(
            id: Uuid::uuid7()->toString(),
            subscriberId: $subscriber->getId(),
            chatId: $message->getChat()->getId(),
            messageId: $message->getMessageId(),
            messageText: $message->getText() ?? '',
            messageDate: (new DateTimeImmutable())->setTimestamp($message->getDate()),
            isBotSender: $message->getFrom() && $message->getFrom()->getIsBot(),
        );
    }
}
