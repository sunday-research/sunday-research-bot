<?php

declare(strict_types=1);

namespace App\Module\BotCommands\Infrastructure\Telegram;

use App\Module\BotCommands\DTO\SendTextMessageDTO;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
use RuntimeException;

readonly class SendMessageClient
{
    /**
     * @phpstan-ignore-next-line property.onlyWritten
     */
    public function __construct(private Telegram $telegram)
    {
    }

    /**
     * @throws RuntimeException
     */
    public function sendTextMessage(SendTextMessageDTO $sendTextMessageDTO): void
    {
        try {
            $response = Request::sendMessage([
                'chat_id' => $sendTextMessageDTO->getChatId(),
                'text' => $sendTextMessageDTO->getText(),
            ]);
        } catch (TelegramException $e) {
            throw new RuntimeException(
                sprintf(
                    'Failed to sendTextMessage: transport error: `%s`',
                    $e->getMessage()
                )
            );
        }
        if (!$response->isOk()) {
            throw new RuntimeException(
                sprintf(
                    'Failed to sendTextMessage: invalid response: error: `%d`, description: `%s`',
                    $response->getErrorCode(),
                    $response->getDescription()
                )
            );
        }
    }
}
