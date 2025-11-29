<?php

declare(strict_types=1);

namespace App\Module\BotCommands\Infrastructure\Telegram;

use App\Module\BotCommands\DTO\DeleteBotCommandsDTO;
use App\Module\BotCommands\DTO\GetBotCommandsDTO;
use App\Module\BotCommands\DTO\SetBotCommandsDTO;
use App\Module\BotCommands\Exception\DeleteBotCommandsException;
use App\Module\BotCommands\Exception\GetBotCommandsException;
use App\Module\BotCommands\Exception\SetBotCommandsException;
use App\Module\BotCommands\ValueObject\BotCommand;
use App\Module\BotCommands\ValueObject\BotCommandsList;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class BotCommandsClient
{
    /**
     * @note Свойство $telegram используется неявно статичным классом \Longman\TelegramBot\Request
     * @phpstan-ignore-next-line property.onlyWritten
     */
    public function __construct(private Telegram $telegram)
    {
    }

    /**
     * @throws GetBotCommandsException
     */
    public function getBotCommands(GetBotCommandsDTO $getBotCommandsDTO): BotCommandsList
    {
        $response = Request::getMyCommands($getBotCommandsDTO->toArray());
        if (!$response->isOk()) {
            throw new GetBotCommandsException(
                sprintf('Failed to getBotCommands, error: `%s`', $response->getDescription()),
                $response->getErrorCode()
            );
        }
        $botCommandsList = new BotCommandsList();
        /** @var \Longman\TelegramBot\Entities\BotCommand $botCommand */
        /** @phpstan-ignore-next-line mixed.type */
        foreach ($response->getResult() as $botCommand) {
            $botCommandsList[] = new BotCommand($botCommand->getCommand(), $botCommand->getDescription());
        }
        return $botCommandsList;
    }

    /**
     * @throws SetBotCommandsException
     */
    public function setBotCommands(SetBotCommandsDTO $setBotCommandsDTO): void
    {
        $response = Request::setMyCommands($setBotCommandsDTO->toArray());
        if (!$response->isOk()) {
            throw new SetBotCommandsException(
                sprintf('Failed to setBotCommands, error: `%s`', $response->getDescription()),
                $response->getErrorCode()
            );
        }
    }

    /**
     * @throws DeleteBotCommandsException
     */
    public function deleteBotCommands(DeleteBotCommandsDTO $deleteBotCommandsDTO): void
    {
        $response = Request::deleteMyCommands($deleteBotCommandsDTO->toArray());
        if (!$response->isOk()) {
            throw new DeleteBotCommandsException(
                sprintf('Failed to deleteBotCommands error: `%s`', $response->getDescription()),
                $response->getErrorCode()
            );
        }
    }
}
