<?php

declare(strict_types=1);

namespace App\Domain\BotCommands\Contract;

use Longman\TelegramBot\Entities\Update;

interface BotCommandHandlerInterface
{
    public function handle(Update $update): void;
}
