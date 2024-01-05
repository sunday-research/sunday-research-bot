<?php

declare(strict_types=1);

namespace App\Domain\BotGetUpdates\Factory;

use App\Domain\BotGetUpdates\Contract\BotCommandHandlerInterface;
use App\Domain\BotGetUpdates\Enum\BotCommandsEnum;
use App\Domain\BotGetUpdates\Handler\BotCommandIssueHandler;

final class BotCommandHandlerFactory
{
    public function createBotCommandHandler(string $botCommandText): ?BotCommandHandlerInterface
    {
        return match ($botCommandText) {
            BotCommandsEnum::ISSUE->value => new BotCommandIssueHandler(),
            default => null,
        };
    }
}
