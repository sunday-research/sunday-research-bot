<?php

declare(strict_types=1);

namespace App\Bundle\BotCommands\Infrastructure\Longman\Repository;

use App\Bundle\BotCommands\Contract\BotCommandsTelegramBotApiRepositoryInterface;
use App\Bundle\BotCommands\DTO\DeleteBotCommandsDTO;
use App\Bundle\BotCommands\DTO\GetBotCommandsDTO;
use App\Bundle\BotCommands\DTO\SetBotCommandsDTO;
use App\Bundle\BotCommands\Exception\DeleteBotCommandsException;
use App\Bundle\BotCommands\Exception\GetBotCommandsException;
use App\Bundle\BotCommands\Exception\SetBotCommandsException;
use App\Bundle\BotCommands\ValueObject\BotCommand;
use App\Bundle\BotCommands\ValueObject\BotCommandsList;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

final readonly class BotCommandsLongmanTelegramBotApiRepository implements BotCommandsTelegramBotApiRepositoryInterface
{
    /**
     * @note Свойство $telegram используется неявно статичным классом \Longman\TelegramBot\Request
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
