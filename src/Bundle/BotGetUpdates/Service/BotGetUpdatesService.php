<?php

declare(strict_types=1);

namespace App\Bundle\BotGetUpdates\Service;

use App\Bundle\BotGetUpdates\Contract\BotGetUpdatesTelegramBotApiRepositoryInterface;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;

final readonly class BotGetUpdatesService
{
    public function __construct(
        private BotGetUpdatesTelegramBotApiRepositoryInterface $botGetUpdatesTelegramBotApiRepository,
    ) {
    }

    /**
     * @throws TelegramException
     */
    public function getUpdates(): ServerResponse
    {
        return $this->botGetUpdatesTelegramBotApiRepository->getUpdates();
    }
}
