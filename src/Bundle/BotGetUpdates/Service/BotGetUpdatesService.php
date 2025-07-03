<?php

declare(strict_types=1);

namespace App\Bundle\BotGetUpdates\Service;

use App\Bundle\BotGetUpdates\Contract\BotGetUpdatesTelegramBotApiRepositoryInterface;
use App\Bundle\BotGetUpdates\Entity\Subscriber;
use App\Bundle\BotGetUpdates\Entity\SubscriberMessage;
use App\Bundle\BotGetUpdates\Factory\SubscriberFactory;
use App\Bundle\BotGetUpdates\Factory\SubscriberMessageFactory;
use App\Bundle\BotGetUpdates\Infrastructure\Doctrine\Mapper\SubscriberMapper;
use App\Bundle\BotGetUpdates\Infrastructure\Doctrine\Mapper\SubscriberMessageMapper;
use App\Bundle\BotGetUpdates\Infrastructure\Doctrine\Repository\SubscriberMessageRepository;
use App\Bundle\BotGetUpdates\Infrastructure\Doctrine\Repository\SubscriberRepository;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\User;
use Longman\TelegramBot\Exception\TelegramException;
use RuntimeException;

final readonly class BotGetUpdatesService
{
    public function __construct(
        private BotGetUpdatesTelegramBotApiRepositoryInterface $botGetUpdatesTelegramBotApiRepository,
        private SubscriberRepository $subscriberRepository,
        private SubscriberMessageRepository $subscriberMessageRepository,
    ) {
    }

    /**
     * @throws TelegramException
     */
    public function getUpdates(): ServerResponse
    {
        return $this->botGetUpdatesTelegramBotApiRepository->getUpdates();
    }

    public function createSubscriber(User $user): Subscriber
    {
        $domainSubscriber = SubscriberFactory::fromTelegramUser($user);
        $doctrineSubscriber = SubscriberMapper::map($domainSubscriber);
        $this->subscriberRepository->create($doctrineSubscriber);

        return $domainSubscriber;
    }

    /**
     * @throws RuntimeException if Subscriber not found in the database
     */
    public function addSubscriberMessage(Message $message, Subscriber $domainSubscriber): SubscriberMessage
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
