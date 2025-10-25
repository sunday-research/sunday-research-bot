<?php

declare(strict_types=1);

namespace App\Module\BotCommands\Service;

use App\Module\BotCommands\DTO\DeleteBotCommandsDTO;
use App\Module\BotCommands\DTO\GetBotCommandsDTO;
use App\Module\BotCommands\DTO\SetBotCommandsDTO;
use App\Module\BotCommands\Exception\DeleteBotCommandsException;
use App\Module\BotCommands\Exception\GetBotCommandsException;
use App\Module\BotCommands\Exception\SetBotCommandsException;
use App\Module\BotCommands\Infrastructure\Redis\Repository\BotCommandsCacheRepository;
use App\Module\BotCommands\Infrastructure\Telegram\BotCommandsClient;
use App\Module\BotCommands\ValueObject\BotCommand;
use App\Module\BotCommands\ValueObject\BotCommandsList;

class BotCommandsService
{
    public function __construct(
        private BotCommandsCacheRepository $botCommandsCacheRepository,
        private BotCommandsClient $botCommandsClient,
    ) {
    }

    /**
     * @throws GetBotCommandsException
     */
    public function getBotCommands(GetBotCommandsDTO $getBotCommandsDTO): BotCommandsList
    {
        if ($this->botCommandsCacheRepository->isBotCommandsCached($getBotCommandsDTO)) {
            return $this->botCommandsCacheRepository->getBotCommands($getBotCommandsDTO);
        }

        $botCommandsList = $this->botCommandsClient->getBotCommands($getBotCommandsDTO);
        $this->botCommandsCacheRepository->setBotCommands(
            SetBotCommandsDTO::makeDTO($botCommandsList->toArray(), $getBotCommandsDTO->getScope()->toArray())
        );

        return $botCommandsList;
    }

    /**
     * @throws SetBotCommandsException
     */
    public function setBotCommands(SetBotCommandsDTO $setBotCommandsDTO): void
    {
        $this->botCommandsClient->setBotCommands($setBotCommandsDTO);
        $this->botCommandsCacheRepository->setBotCommands($setBotCommandsDTO);
    }

    /**
     * @throws DeleteBotCommandsException
     */
    public function deleteBotCommands(DeleteBotCommandsDTO $deleteBotCommandsDTO): void
    {
        $this->botCommandsClient->deleteBotCommands($deleteBotCommandsDTO);
        $this->botCommandsCacheRepository->deleteBotCommands($deleteBotCommandsDTO);
    }

    public function findCommandByCommandText(
        string $commandText,
        string $botUsername,
        GetBotCommandsDTO $getBotCommandsDTO
    ): ?BotCommand {
        $botCommandsList = $this->getBotCommands($getBotCommandsDTO);
        /** @var BotCommand $botCommand */
        foreach ($botCommandsList as $botCommand) {
            if (
                $commandText === $botCommand->getCommand()
                || $commandText === sprintf('%s@%s', $botCommand->getCommand(), $botUsername)
            ) {
                return $botCommand;
            }
        }
        return null;
    }
}
