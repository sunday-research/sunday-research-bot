<?php

declare(strict_types=1);

namespace App\Module\BotCommands\DTO;

use App\Module\BotCommands\Exception\BotCommandScopeValidationException;
use App\Module\BotCommands\Exception\BotCommandsListAddCommandException;
use App\Module\BotCommands\ValueObject\BotCommand;
use App\Module\BotCommands\ValueObject\BotCommandScope;
use App\Module\BotCommands\ValueObject\BotCommandsList;
use TypeError;

final class SetBotCommandsDTO
{
    private BotCommandsList $commands;
    private BotCommandScope $scope;

    /**
     * @param array<int, array<string, string|null>> $botCommands
     * @param array<string, string|null>|null $scope
     * @throws BotCommandsListAddCommandException
     * @throws BotCommandScopeValidationException
     */
    private function __construct(array $botCommands, ?array $scope = null)
    {
        $botCommandsList = new BotCommandsList();
        foreach ($botCommands as $botCommand) {
            try {
                if (empty($botCommand['command']) || empty($botCommand['description'])) {
                    continue;
                }
                $botCommandsList[] = new BotCommand(
                    (string) $botCommand['command'],
                    (string) $botCommand['description']
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
     * @param array<int, array<string, string|null>> $botCommands
     * @param array<string, string|null>|null $scope
     * @todo: отказаться от статического метода создания DTO, использовать конструктор напрямую
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

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'commands' => $this->getCommands()->toArray(),
            'scope' => $this->getScope()->toArray(),
        ];
    }
}
