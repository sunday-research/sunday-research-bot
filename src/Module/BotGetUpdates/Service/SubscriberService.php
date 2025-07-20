<?php

declare(strict_types=1);

namespace App\Module\BotGetUpdates\Service;

use App\Module\BotGetUpdates\Entity\Subscriber;
use App\Module\BotGetUpdates\Factory\SubscriberFactory;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Mapper\SubscriberMapper;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Repository\SubscriberRepository;
use Longman\TelegramBot\Entities\User;

readonly class SubscriberService
{
    public function __construct(
        private SubscriberRepository $subscriberRepository,
    ) {
    }

    public function create(User $user): Subscriber
    {
        $domainSubscriber = SubscriberFactory::fromTelegramUser($user);
        $doctrineSubscriber = SubscriberMapper::map($domainSubscriber);
        $this->subscriberRepository->create($doctrineSubscriber);

        return $domainSubscriber;
    }
}
