<?php

declare(strict_types=1);

namespace App\Bundle\BotCommands\Contract;

use App\Bundle\BotCommands\DTO\DeleteBotCommandsDTO;
use App\Bundle\BotCommands\DTO\GetBotCommandsDTO;
use App\Bundle\BotCommands\DTO\SetBotCommandsDTO;
use App\Bundle\BotCommands\ValueObject\BotCommandsList;

interface BotCommandsCacheRepositoryInterface
{
    public function isBotCommandsCached(GetBotCommandsDTO $getBotCommandsDTO): bool;

    public function getBotCommands(GetBotCommandsDTO $getBotCommandsDTO): BotCommandsList;

    public function setBotCommands(SetBotCommandsDTO $setBotCommandsDTO, ?int $ttlInSeconds = null): void;

    public function deleteBotCommands(DeleteBotCommandsDTO $deleteBotCommandsDTO): void;
}
