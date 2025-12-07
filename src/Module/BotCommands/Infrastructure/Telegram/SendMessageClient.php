<?php

declare(strict_types=1);

namespace App\Module\BotCommands\Infrastructure\Telegram;

use App\Module\BotCommands\DTO\SendTextMessageDTO;
use App\Module\BotCommands\DTO\SendMediaMessageDTO;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
use RuntimeException;

class SendMessageClient
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

    /**
     * @throws RuntimeException
     */
    public function sendMediaMessage(SendMediaMessageDTO $dto): void
    {
        try {
            $params = [
                'chat_id' => $dto->getChatId(),
                'caption' => $dto->getCaption(),
                'parse_mode' => $dto->getParseMode(),
            ];
            if ($dto->getReplyMarkup()) {
                $params['reply_markup'] = $dto->getReplyMarkup();
            }
            switch ($dto->getMediaType()) {
                case 'animation':
                    $params['animation'] = $dto->getMedia();
                    $response = Request::sendAnimation($params);
                    break;
                case 'document':
                    $params['document'] = $dto->getMedia();
                    $response = Request::sendDocument($params);
                    break;
                case 'photo':
                default:
                    $params['photo'] = $dto->getMedia();
                    $response = Request::sendPhoto($params);
                    break;
            }
        } catch (TelegramException $e) {
            throw new RuntimeException(
                sprintf(
                    'Failed to sendMediaMessage: transport error: `%s`',
                    $e->getMessage()
                )
            );
        }
        if (!$response->isOk()) {
            throw new RuntimeException(
                sprintf(
                    'Failed to sendMediaMessage: invalid response: error: `%d`, description: `%s`',
                    $response->getErrorCode(),
                    $response->getDescription()
                )
            );
        }
    }
}
