<?php

declare(strict_types=1);

namespace App\Bundle\BotGetUpdates\Contract;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;

interface BotGetUpdatesTelegramBotApiRepositoryInterface
{
    /**
     * @throws TelegramException
     */
    public function getUpdates(): ServerResponse;
}
