<?php

declare(strict_types=1);

namespace App\Module\BotCommands\MessageHandler;

use App\Module\BotCommands\Message\BotCommandIssueMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class BotCommandIssueMessageHandler
{
    public function __invoke(BotCommandIssueMessage $botCommandIssueMessage): void
    {
        // todo: запишем поступившее предложение от пользователя в БД
        file_put_contents('/tmp/bot_command_issue_message_handler.log', 'test', FILE_APPEND);
    }
}
