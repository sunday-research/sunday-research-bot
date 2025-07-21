<?php

declare(strict_types=1);

namespace App\Tests\Module\BotCommands\DTO;

use App\Module\BotCommands\DTO\BaseBotCommandsDTO;
use App\Module\BotCommands\Exception\BotCommandScopeValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class BaseBotCommandsDTOTest extends TestCase
{
    /**
     * @note scope_type, chat_id, user_id
     * @return array<int, array{0: string, 1: string|null, 2: string|null}>
     */
    public static function createBaseBotCommandsDTOSuccessfullyDataProvider(): array
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
     * @return array<int, array{0: string, 1: string|null, 2: string|null}>
     */
    public static function createBaseBotCommandsDTOFailDataProvider(): array
    {
        return [
            ['chat', null, null],
            ['chat_administrators', null, null],
            ['chat_member', '123456789', null],
        ];
    }

    #[DataProvider('createBaseBotCommandsDTOSuccessfullyDataProvider')]
    public function testCreateBaseBotCommandsDTOSuccessfully(
        string $scopeType,
        ?string $chatId = null,
        ?string $userId = null
    ): void {
        $baseBotCommandsDTO = BaseBotCommandsDTO::makeDTO([
            'type' => $scopeType,
            'chat_id' => $chatId,
            'user_id' => $userId,
        ]);
        $this->assertInstanceOf(BaseBotCommandsDTO::class, $baseBotCommandsDTO, 'Unexpected result');
    }

    #[DataProvider('createBaseBotCommandsDTOFailDataProvider')]
    public function testCreateBaseBotCommandsDTOFail(
        string $scopeType,
        ?string $chatId = null,
        ?string $userId = null
    ): void {
        $this->expectException(BotCommandScopeValidationException::class);
        BaseBotCommandsDTO::makeDTO([
            'type' => $scopeType,
            'chat_id' => $chatId,
            'user_id' => $userId,
        ]);
    }
}
