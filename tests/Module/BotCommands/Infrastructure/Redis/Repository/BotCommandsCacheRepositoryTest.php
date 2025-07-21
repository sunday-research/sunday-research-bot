<?php

declare(strict_types=1);

namespace App\Tests\Module\BotCommands\Infrastructure\Redis\Repository;

use App\Module\BotCommands\DTO\GetBotCommandsDTO;
use App\Module\BotCommands\Infrastructure\Redis\Repository\BotCommandsCacheRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Predis\Client;

final class BotCommandsCacheRepositoryTest extends TestCase
{
    /**
     * @note scope_type, is_exists, chat_id, user_id
     * @return array<int, array{0: string, 1: int, 2: string|null, 3: string|null}>
     */
    public static function isBotCommandsCachedSuccessfullyDataProvider(): array
    {
        return [
            ['default', 1, null, null],
            ['all_private_chats', 1, null, null],
            ['all_group_chats', 0, null, null],
            ['all_chat_administrators', 0, null, null],
            ['chat', 1, '123456789', null],
            ['chat_administrators', 1, '123456789', null],
            ['chat_member', 0, '123456789', '12345'],
        ];
    }

    /**
     * @note scope_type, result_data [command => description, ...], chat_id, user_id
     * @return array<int, array{0: string, 1: array<string, string>, 2: string|null, 3: string|null}>
     */
    public static function getBotCommandsSuccessfullyDataProvider(): array
    {
        return [
            ['default', ['/first' => 'First command', '/second' => 'Second command'], null, null],
            ['all_private_chats', [], null, null],
            ['all_group_chats', ['/test_cmd' => 'Test command'], null, null],
            ['all_chat_administrators', [], null, null],
            ['chat', [], '123456789', null],
            ['chat_administrators', ['/cmd3' => '3 cmd', '/cmd4' => '4 cmd'], '123456789', null],
            ['chat_member', [], '123456789', '12345'],
        ];
    }

    #[DataProvider('isBotCommandsCachedSuccessfullyDataProvider')]
    public function testIsBotCommandsCachedSuccessfully(
        string $scopeType,
        int $isExists,
        ?string $chatId = null,
        ?string $userId = null
    ): void {
        $predisClientMock = $this->getMockBuilder(Client::class)
            ->addMethods(['exists'])
            ->getMock();
        $predisClientMock->expects($this->exactly(1))
            ->method('exists')
            ->willReturn($isExists);
        $repository = new BotCommandsCacheRepository($predisClientMock);
        $this->assertEquals(
            (bool)$isExists,
            $repository->isBotCommandsCached(
                GetBotCommandsDTO::makeDTO([
                    'type' => $scopeType,
                    'chat_id' => $chatId,
                    'user_id' => $userId
                ])
            ),
            'Unexpected result'
        );
    }

    /**
     * @param array<string, string> $resultData
     */
    #[DataProvider('getBotCommandsSuccessfullyDataProvider')]
    public function testGetBotCommandsSuccessfully(
        string $scopeType,
        array $resultData,
        ?string $chatId = null,
        ?string $userId = null
    ): void {
        $predisClientMock = $this->getMockBuilder(Client::class)
            ->addMethods(['hgetall'])
            ->getMock();
        $predisClientMock->expects($this->exactly(1))
            ->method('hgetall')
            ->willReturn($resultData);
        $repository = new BotCommandsCacheRepository($predisClientMock);
        $resultDataForAssert = [];
        foreach ($resultData as $command => $description) {
            $resultDataForAssert[] = [
                'command' => $command,
                'description' => $description,
            ];
        }
        $this->assertEquals(
            $resultDataForAssert,
            $repository->getBotCommands(
                GetBotCommandsDTO::makeDTO([
                    'type' => $scopeType,
                    'chat_id' => $chatId,
                    'user_id' => $userId
                ])
            )->toArray(),
            'Unexpected result'
        );
    }
}
