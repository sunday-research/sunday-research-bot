<?php

declare(strict_types=1);

namespace App\Bundle\BotCommands\Contract;

use App\Bundle\BotCommands\DTO\DeleteBotCommandsDTO;
use App\Bundle\BotCommands\DTO\GetBotCommandsDTO;
use App\Bundle\BotCommands\DTO\SetBotCommandsDTO;
use App\Bundle\BotCommands\Exception\DeleteBotCommandsException;
use App\Bundle\BotCommands\Exception\GetBotCommandsException;
use App\Bundle\BotCommands\Exception\SetBotCommandsException;
use App\Bundle\BotCommands\ValueObject\BotCommandsList;

interface BotCommandsTelegramBotApiRepositoryInterface
{
    /**
     * @throws GetBotCommandsException
     */
    public function getBotCommands(GetBotCommandsDTO $getBotCommandsDTO): BotCommandsList;

    /**
     * @throws SetBotCommandsException
     */
    public function setBotCommands(SetBotCommandsDTO $setBotCommandsDTO): void;

    /**
     * @throws DeleteBotCommandsException
     */
    public function deleteBotCommands(DeleteBotCommandsDTO $deleteBotCommandsDTO): void;
}
