<?php

declare(strict_types=1);

namespace App\Bundle\Share\Infrastructure\Longman\Repository;

use App\Bundle\Share\Contract\TelegramBotApiRepositoryInterface;
use App\Bundle\Share\DTO\SendTextMessageDTO;
use App\Bundle\Share\Exception\TelegramBotApiSendTextMessageException;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

readonly class LongmanTelegramBotApiRepository implements TelegramBotApiRepositoryInterface
{
    public function __construct(private Telegram $telegram)
    {
    }

    /**
     * @throws TelegramBotApiSendTextMessageException
     * @todo: реализовать билдер, который будет собирать все возможные вариации текстового сообщения
     */
    public function sendTextMessage(SendTextMessageDTO $sendTextMessageDTO): void
    {
        try {
            $response = Request::sendMessage([
                'chat_id' => $sendTextMessageDTO->getChatId(),
                'text' => $sendTextMessageDTO->getText(),
            ]);
        } catch (TelegramException $e) {
            throw new TelegramBotApiSendTextMessageException(
                sprintf(
                    'Failed to sendTextMessage: transport error: `%s`',
                    $e->getMessage()
                )
            );
        }
        if (!$response->isOk()) {
            throw new TelegramBotApiSendTextMessageException(
                sprintf(
                    'Failed to sendTextMessage: invalid response: error: `%d`, description: `%s`',
                    $response->getErrorCode(),
                    $response->getDescription()
                )
            );
        }
    }
}
