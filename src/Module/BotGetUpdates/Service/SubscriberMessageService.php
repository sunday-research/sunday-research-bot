<?php

declare(strict_types=1);

namespace App\Module\BotGetUpdates\Service;

use App\Module\BotGetUpdates\Entity\Subscriber;
use App\Module\BotGetUpdates\Entity\SubscriberMessage;
use App\Module\BotGetUpdates\Exception\InvalidSubscriberMessageException;
use App\Module\BotGetUpdates\Exception\SubscriberMessageNotFoundException;
use App\Module\BotGetUpdates\Exception\SubscriberNotFoundException;
use App\Module\BotGetUpdates\Factory\SubscriberMessageFactory;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Builder\SubscriberMessageBuilder;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Mapper\SubscriberMessageMapper;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Repository\SubscriberMessageRepository;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Repository\SubscriberRepository;
use Longman\TelegramBot\Entities\Message;

readonly class SubscriberMessageService
{
    public function __construct(
        private SubscriberRepository $subscriberRepository,
        private SubscriberMessageRepository $subscriberMessageRepository,
    ) {
    }

    /**
     * @throws SubscriberNotFoundException if Subscriber not found in the database
     */
    public function create(Message $message, Subscriber $domainSubscriber): SubscriberMessage
    {
        $doctrineSubscriber = $this->subscriberRepository->findByTelegramUserId($domainSubscriber->getTelegramUserId());
        if (!$doctrineSubscriber) {
            throw new SubscriberNotFoundException(
                sprintf(
                    'Subscriber with telegramUserId = %d not found in the database.',
                    $domainSubscriber->getTelegramUserId()
                )
            );
        }

        $domainSubscriberMessage = SubscriberMessageFactory::fromTelegramMessage(
            message: $message,
            subscriber: $domainSubscriber,
        );

        $doctrineSubscriberMessage = SubscriberMessageMapper::map($domainSubscriberMessage, $doctrineSubscriber);
        $this->subscriberMessageRepository->create($doctrineSubscriberMessage);

        return $domainSubscriberMessage;
    }

    /**
     * @throws InvalidSubscriberMessageException if message text is empty
     * @throws SubscriberMessageNotFoundException if message not found in the database
     */
    public function update(Message $message): SubscriberMessage
    {
        if (empty($message->getText())) {
            throw new InvalidSubscriberMessageException('Nothing to update, message text is empty');
        }

        $doctrineSubscriberMessage = $this->subscriberMessageRepository->findOneBy([
            'chatId' => $message->getChat()->getId(),
            'messageId' => $message->getMessageId(),
        ]);

        if (!$doctrineSubscriberMessage) {
            throw new SubscriberMessageNotFoundException(
                sprintf(
                    'Message with chatId = %d and messageId = %d not found in the database.',
                    $message->getChat()->getId(),
                    $message->getMessageId()
                )
            );
        }

        $doctrineSubscriberMessage->setMessageText($message->getText());
        $this->subscriberMessageRepository->update($doctrineSubscriberMessage);

        return SubscriberMessageBuilder::build($doctrineSubscriberMessage);
    }
}
