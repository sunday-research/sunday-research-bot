<?php

declare(strict_types=1);

namespace App\Bundle\BotCommands\DTO;

use App\Bundle\BotCommands\Exception\BotCommandScopeValidationException;
use App\Bundle\BotCommands\ValueObject\BotCommandScope;

class BaseBotCommandsDTO
{
    protected BotCommandScope $scope;

    /**
     * @throws BotCommandScopeValidationException
     */
    protected function __construct(?array $scope = null)
    {
        $this->scope = new BotCommandScope(
            $scope['type'] ?? BotCommandScope::BOT_COMMAND_SCOPE_TYPE_DEFAULT,
            $scope['chat_id'] ?? null,
            $scope['user_id'] ?? null
        );
    }

    /**
     * @throws BotCommandScopeValidationException
     */
    public static function makeDTO(?array $scope = null): static
    {
        return (new static($scope));
    }

    public function getScope(): BotCommandScope
    {
        return $this->scope;
    }

    public function toArray(): array
    {
        return [
            'scope' => $this->getScope()->toArray(),
        ];
    }
}
