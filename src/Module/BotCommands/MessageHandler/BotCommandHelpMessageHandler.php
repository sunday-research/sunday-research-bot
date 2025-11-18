<?php

declare(strict_types=1);

namespace App\Module\BotCommands\MessageHandler;

use App\Module\BotCommands\Message\BotCommandHelpMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class BotCommandHelpMessageHandler
{
    public function __invoke(BotCommandHelpMessage $botCommandHelpMessage): void
    {
    }
}
