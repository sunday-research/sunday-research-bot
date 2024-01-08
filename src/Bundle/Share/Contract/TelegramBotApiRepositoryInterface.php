<?php

declare(strict_types=1);

namespace App\Bundle\Share\Contract;

use App\Bundle\Share\DTO\SendTextMessageDTO;
use App\Bundle\Share\Exception\TelegramBotApiSendTextMessageException;

interface TelegramBotApiRepositoryInterface
{
    /**
     * @throws TelegramBotApiSendTextMessageException
     */
    public function sendTextMessage(SendTextMessageDTO $sendTextMessageDTO): void;
}
