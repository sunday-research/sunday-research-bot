<?php

declare(strict_types=1);

namespace App\Module\BotCommands\DTO;

use App\Module\BotCommands\Exception\BotCommandScopeValidationException;
use App\Module\BotCommands\ValueObject\BotCommandScope;

/**
 * @see \App\Tests\Module\BotCommands\DTO\BaseBotCommandsDTOTest
 */
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
     * @todo: отказаться от статического метода, использовать конструктор
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
