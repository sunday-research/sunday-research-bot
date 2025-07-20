<?php

declare(strict_types=1);

namespace App\Module\BotGetUpdates\Service;

use App\Module\BotGetUpdates\Infrastructure\Telegram\BotGetUpdatesClient;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;

final readonly class BotGetUpdatesService
{
    public function __construct(
        private BotGetUpdatesClient $telegramBotApiClient,
    ) {
    }

    /**
     * @throws TelegramException
     */
    public function getUpdates(): ServerResponse
    {
        return $this->telegramBotApiClient->getUpdates();
    }
}
