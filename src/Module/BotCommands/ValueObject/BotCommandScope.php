<?php

declare(strict_types=1);

namespace App\Module\BotCommands\ValueObject;

use App\Module\BotCommands\Exception\BotCommandScopeValidationException;

/**
 * @see \App\Tests\Module\BotCommands\ValueObject\BotCommandScopeTest
 */
final readonly class BotCommandScope
{
    public const BOT_COMMAND_SCOPE_TYPE_DEFAULT = 'default';
    private const BOT_COMMAND_SCOPE_TYPE_CHAT_MEMBER = 'chat_member';

    private const ALLOWED_BOT_COMMAND_SCOPE_TYPES = [
        self::BOT_COMMAND_SCOPE_TYPE_DEFAULT,
        'all_private_chats',
        'all_group_chats',
        'all_chat_administrators',
        'chat',
        'chat_administrators',
        self::BOT_COMMAND_SCOPE_TYPE_CHAT_MEMBER,
    ];

    private const BOT_COMMAND_SCOPE_TYPES_WITH_CHAT_ID = [
        'chat',
        'chat_administrators',
        self::BOT_COMMAND_SCOPE_TYPE_CHAT_MEMBER,
    ];

    private string $type;

    /**
     * @throws BotCommandScopeValidationException
     */
    public function __construct(
        string $type = self::BOT_COMMAND_SCOPE_TYPE_DEFAULT,
        private ?string $chatId = null,
        private ?string $userId = null
    ) {
        $this->validate($type);
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getChatId(): ?string
    {
        return $this->chatId;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    /**
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        $result = [
            'type' => $this->getType(),
        ];

        if ($this->getChatId()) {
            $result['chat_id'] = $this->getChatId();
        }
        if ($this->getUserId()) {
            $result['user_id'] = $this->getUserId();
        }

        return $result;
    }

    /**
     * @throws BotCommandScopeValidationException
     * @todo использовать компонент symfony/validator
     * @see https://symfony.com/doc/current/validation/custom_constraint.html
     */
    private function validate(string $type): void
    {
        if (!in_array($type, self::ALLOWED_BOT_COMMAND_SCOPE_TYPES, true)) {
            throw new BotCommandScopeValidationException(
                sprintf('BotCommand type `%s` not allowed', $type)
            );
        }

        if (
            in_array($type, self::BOT_COMMAND_SCOPE_TYPES_WITH_CHAT_ID, true)
            && null === $this->chatId
        ) {
            throw new BotCommandScopeValidationException('BotCommand chat_id must not be null');
        }

        if (
            self::BOT_COMMAND_SCOPE_TYPE_CHAT_MEMBER === $type
            && (null === $this->chatId || null === $this->userId)
        ) {
            throw new BotCommandScopeValidationException('BotCommand chat_id must not be null');
        }
    }
}
