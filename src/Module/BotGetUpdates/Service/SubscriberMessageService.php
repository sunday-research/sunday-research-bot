<?php

declare(strict_types=1);

namespace App\Module\BotGetUpdates\Service;

use App\Module\BotGetUpdates\Entity\Subscriber;
use App\Module\BotGetUpdates\Entity\SubscriberMessage;
use App\Module\BotGetUpdates\Factory\SubscriberMessageFactory;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Mapper\SubscriberMessageMapper;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Repository\SubscriberMessageRepository;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Repository\SubscriberRepository;
use Longman\TelegramBot\Entities\Message;
use RuntimeException;

readonly class SubscriberMessageService
{
    public function __construct(
        private SubscriberRepository $subscriberRepository,
        private SubscriberMessageRepository $subscriberMessageRepository,
    ) {
    }

    /**
     * @throws RuntimeException if Subscriber not found in the database
     */
    public function create(Message $message, Subscriber $domainSubscriber): SubscriberMessage
    {
        $doctrineSubscriber = $this->subscriberRepository->findByTelegramUserId($domainSubscriber->getTelegramUserId());
        if (!$doctrineSubscriber) {
            throw new RuntimeException(
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
}
