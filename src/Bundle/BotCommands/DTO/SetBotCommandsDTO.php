<?php

declare(strict_types=1);

namespace App\Bundle\BotCommands\DTO;

use App\Bundle\BotCommands\Exception\BotCommandScopeValidationException;
use App\Bundle\BotCommands\Exception\BotCommandsListAddCommandException;
use App\Bundle\BotCommands\ValueObject\BotCommand;
use App\Bundle\BotCommands\ValueObject\BotCommandScope;
use App\Bundle\BotCommands\ValueObject\BotCommandsList;
use TypeError;

final class SetBotCommandsDTO
{
    private BotCommandsList $commands;
    private BotCommandScope $scope;

    /**
     * @throws BotCommandsListAddCommandException
     * @throws BotCommandScopeValidationException
     */
    private function __construct(array $botCommands, ?array $scope = null)
    {
        $botCommandsList = new BotCommandsList();
        foreach ($botCommands as $botCommand) {
            try {
                $botCommandsList[] = new BotCommand(
                    $botCommand['command'] ?? null,
                    $botCommand['description'] ?? null
                );
            } catch (TypeError $e) {
                throw new BotCommandsListAddCommandException(
                    'Invalid BotCommand argument: ' . $e->getMessage()
                );
            }
        }
        $this->commands = $botCommandsList;
        $this->scope = new BotCommandScope(
            $scope['type'] ?? BotCommandScope::BOT_COMMAND_SCOPE_TYPE_DEFAULT,
            $scope['chat_id'] ?? null,
            $scope['user_id'] ?? null
        );
    }

    /**
     * @throws BotCommandsListAddCommandException
     * @throws BotCommandScopeValidationException
     */
    public static function makeDTO(array $botCommands, ?array $scope = null): self
    {
        return new self($botCommands, $scope);
    }

    public function getCommands(): BotCommandsList
    {
        return $this->commands;
    }

    public function getScope(): BotCommandScope
    {
        return $this->scope;
    }

    public function toArray(): array
    {
        return [
            'commands' => $this->getCommands()->toArray(),
            'scope' => $this->getScope()->toArray(),
        ];
    }
}
