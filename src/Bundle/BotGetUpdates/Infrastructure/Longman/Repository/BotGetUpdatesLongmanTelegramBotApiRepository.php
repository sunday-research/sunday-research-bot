<?php

declare(strict_types=1);

namespace App\Bundle\BotGetUpdates\Infrastructure\Longman\Repository;

use App\Bundle\BotGetUpdates\Contract\BotGetUpdatesTelegramBotApiRepositoryInterface;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;

final readonly class BotGetUpdatesLongmanTelegramBotApiRepository implements BotGetUpdatesTelegramBotApiRepositoryInterface
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
