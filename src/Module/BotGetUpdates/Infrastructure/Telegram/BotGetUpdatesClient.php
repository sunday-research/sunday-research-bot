<?php

declare(strict_types=1);

namespace App\Module\BotGetUpdates\Infrastructure\Telegram;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;

readonly class BotGetUpdatesClient
{
    public function __construct(private Telegram $telegram)
    {
    }

    /**
     * @throws TelegramException
     */
    public function getUpdates(): ServerResponse
    {
        return $this->telegram->handleGetUpdates([]);
    }
}
