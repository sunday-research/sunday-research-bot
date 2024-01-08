<?php

declare(strict_types=1);

namespace App\Tests\Bundle\BotCommands\ValueObject;

use App\Bundle\BotCommands\Exception\BotCommandScopeValidationException;
use App\Bundle\BotCommands\ValueObject\BotCommandScope;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class BotCommandScopeTest extends TestCase
{
    /**
     * @note scope_type, chat_id, user_id
     */
    public static function createBotCommandScopeSuccessfullyDataProvider(): array
    {
        return [
            ['default', null, null],
            ['all_private_chats', null, null],
            ['all_group_chats', null, null],
            ['all_chat_administrators', null, null],
            ['chat', '123456789', null],
            ['chat_administrators', '123456789', null],
            ['chat_member', '123456789', '12345'],
        ];
    }

    /**
     * @note scope_type, chat_id, user_id
     */
    public static function createBotCommandScopeFailDataProvider(): array
    {
        return [
            ['chat', null, null],
            ['chat_administrators', null, null],
            ['chat_member', '123456789', null],
        ];
    }

    #[DataProvider('createBotCommandScopeSuccessfullyDataProvider')]
    public function testCreateBotCommandScopeSuccessfully(string $scopeType, ?string $chatId, ?string $userId): void
    {
        $botCommandScope = new BotCommandScope($scopeType, $chatId, $userId);
        $this->assertTrue($botCommandScope instanceof BotCommandScope, 'Unexpected result');
    }

    #[DataProvider('createBotCommandScopeFailDataProvider')]
    public function testCreateBotCommandScopeFail(string $scopeType, ?string $chatId, ?string $userId): void
    {
        $this->expectException(BotCommandScopeValidationException::class);
        new BotCommandScope($scopeType, $chatId, $userId);
    }
}
