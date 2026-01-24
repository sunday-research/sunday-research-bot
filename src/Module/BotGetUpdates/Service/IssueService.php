<?php

declare(strict_types=1);

namespace App\Module\BotGetUpdates\Service;

use App\Module\BotCommands\Enum\BotCommandsEnum;
use App\Module\BotGetUpdates\Entity\Subscriber;
use App\Module\BotGetUpdates\Factory\IssueFactory;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Mapper\IssueMapper;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Model\Issue as DoctrineIssue;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Repository\IssueRepository;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Repository\SubscriberRepository;
use InvalidArgumentException;
use Longman\TelegramBot\Entities\Message;
use RuntimeException;

readonly class IssueService
{
    public function __construct(
        private SubscriberRepository $subscriberRepository,
        private IssueRepository $issueRepository,
    ) {
    }

    public function create(Message $message, Subscriber $domainSubscriber): DoctrineIssue
    {
        $this->validateIssueCommand($message);
        $doctrineSubscriber = $this->subscriberRepository->findByTelegramUserId($domainSubscriber->getTelegramUserId());

        if (!$doctrineSubscriber) {
            throw new RuntimeException(
                sprintf(
                    'Subscriber with telegramUserId = %d not found in the database.',
                    $domainSubscriber->getTelegramUserId()
                )
            );
        }

        $domainIssueMessage = IssueFactory::fromTelegramMessage(
            message: $message,
            subscriber: $domainSubscriber,
        );
        $doctrineIssue = IssueMapper::map($domainIssueMessage, $doctrineSubscriber);
        $this->issueRepository->create($doctrineIssue);

        return $doctrineIssue;
    }

    private function validateIssueCommand(Message $message): void
    {
        $textFromMessage = $message->getText();
        $text = $textFromMessage === null ? '' : trim($textFromMessage);
        $command = BotCommandsEnum::ISSUE->value;
        $pattern = '/^\/' . preg_quote($command, '/') . '\s+\S+/';

        if (!preg_match($pattern, $text)) {
            throw new InvalidArgumentException(
                sprintf('Use: /%s <description>', $command)
            );
        }
    }
}
