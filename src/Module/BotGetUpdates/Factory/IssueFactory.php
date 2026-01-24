<?php

declare(strict_types=1);

namespace App\Module\BotGetUpdates\Factory;

use App\Module\BotCommands\Enum\IssuePriority;
use App\Module\BotCommands\Enum\IssueStatus;
use App\Module\BotGetUpdates\Entity\Issue;
use App\Module\BotGetUpdates\Entity\Subscriber;
use Longman\TelegramBot\Entities\Message;
use Ramsey\Uuid\Uuid;

class IssueFactory
{
    public static function fromTelegramMessage(Message $message, Subscriber $subscriber): Issue
    {
        $text = $message->getText();

        if (!is_string($text)) {
            throw new \InvalidArgumentException('Message text must be a string');
        }

        $description = preg_replace('/^\/issue\s+/', '', $text);

        if (!is_string($description)) {
            throw new \RuntimeException('Failed to process message text');
        }

        $description = trim($description);

        if ($description === '') {
            throw new \InvalidArgumentException('Issue description cannot be empty');
        }

        return new Issue(
            id: Uuid::uuid7()->toString(),
            chatId: $message->getChat()->getId(),
            messageId: $message->getMessageId(),
            messageText: $description,
            decisionComment: null,
            createdAt: (new \DateTimeImmutable())->setTimestamp($message->getDate()),
            updatedAt: (new \DateTimeImmutable())->setTimestamp($message->getDate()),
            resolvedAt: null,
            status: IssueStatus::NEW,
            priority: IssuePriority::MEDIUM,
        );
    }
}
