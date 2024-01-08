<?php

declare(strict_types=1);

namespace App\Bundle\BotCommands\Service;

use App\Bundle\BotCommands\Contract\BotCommandsCacheRepositoryInterface;
use App\Bundle\BotCommands\Contract\BotCommandsTelegramBotApiRepositoryInterface;
use App\Bundle\BotCommands\DTO\DeleteBotCommandsDTO;
use App\Bundle\BotCommands\DTO\GetBotCommandsDTO;
use App\Bundle\BotCommands\DTO\SetBotCommandsDTO;
use App\Bundle\BotCommands\Exception\DeleteBotCommandsException;
use App\Bundle\BotCommands\Exception\GetBotCommandsException;
use App\Bundle\BotCommands\Exception\SetBotCommandsException;
use App\Bundle\BotCommands\ValueObject\BotCommand;
use App\Bundle\BotCommands\ValueObject\BotCommandsList;

final readonly class BotCommandsService
{
    public function __construct(
        private BotCommandsCacheRepositoryInterface $botCommandsCacheRepository,
        private BotCommandsTelegramBotApiRepositoryInterface $botCommandsTelegramBotApiRepository,
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

        $botCommandsList = $this->botCommandsTelegramBotApiRepository->getBotCommands($getBotCommandsDTO);
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
        $this->botCommandsTelegramBotApiRepository->setBotCommands($setBotCommandsDTO);
        $this->botCommandsCacheRepository->setBotCommands($setBotCommandsDTO);
    }

    /**
     * @throws DeleteBotCommandsException
     */
    public function deleteBotCommands(DeleteBotCommandsDTO $deleteBotCommandsDTO): void
    {
        $this->botCommandsTelegramBotApiRepository->deleteBotCommands($deleteBotCommandsDTO);
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
