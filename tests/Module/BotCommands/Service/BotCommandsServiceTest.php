<?php

namespace App\Tests\Unit\Module\BotCommands\Service;

use App\Module\BotCommands\DTO\GetBotCommandsDTO;
use App\Module\BotCommands\DTO\SetBotCommandsDTO;
use App\Module\BotCommands\Infrastructure\Redis\Repository\BotCommandsCacheRepository;
use App\Module\BotCommands\Infrastructure\Telegram\BotCommandsClient;
use App\Module\BotCommands\Service\BotCommandsService;
use App\Module\BotCommands\ValueObject\BotCommand;
use App\Module\BotCommands\ValueObject\BotCommandsList;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BotCommandsServiceTest extends TestCase
{
    private BotCommandsService $service;
    private MockObject $cacheRepository;
    private MockObject $botCommandsClient;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->cacheRepository = $this->createMock(BotCommandsCacheRepository::class);
        $this->botCommandsClient = $this->createMock(BotCommandsClient::class);

        $this->service = new BotCommandsService(
            $this->cacheRepository,
            $this->botCommandsClient
        );
    }

    public function testFindHelpCommandReturnsCorrectCommand(): void
    {
        $commandsList = new BotCommandsList();
        $commandsList[] = new BotCommand('help', 'Показать справку');
        $commandsList[] = new BotCommand('issue', 'Сообщить о проблеме');

        $this->cacheRepository->method('isBotCommandsCached')->willReturn(true);
        $this->cacheRepository->method('getBotCommands')->willReturn($commandsList);

        $result = $this->service->findCommandByCommandText(
            'help',
            'test_bot',
            GetBotCommandsDTO::makeDTO()
        );

        $this->assertInstanceOf(BotCommand::class, $result);
        $this->assertEquals('help', $result->getCommand());
    }

    public function testFindHelpCommandWithBotUsername(): void
    {
        $commandsList = new BotCommandsList();
        $commandsList[] = new BotCommand('help', 'Показать справку');

        $this->cacheRepository->method('isBotCommandsCached')->willReturn(true);
        $this->cacheRepository->method('getBotCommands')->willReturn($commandsList);

        $result = $this->service->findCommandByCommandText(
            'help@test_bot',
            'test_bot',
            GetBotCommandsDTO::makeDTO()
        );

        $this->assertInstanceOf(BotCommand::class, $result);
        $this->assertEquals('help', $result->getCommand());
    }

    public function testFindUnknownCommandReturnsNull(): void
    {
        $commandsList = new BotCommandsList();
        $commandsList[] = new BotCommand('help', 'Показать справку');

        $this->cacheRepository->method('isBotCommandsCached')->willReturn(true);
        $this->cacheRepository->method('getBotCommands')->willReturn($commandsList);

        $result = $this->service->findCommandByCommandText(
            'unknown',
            'test_bot',
            GetBotCommandsDTO::makeDTO()
        );

        $this->assertNull($result);
    }

    public function testGetBotCommandsUsesCacheWhenAvailable(): void
    {
        $cachedCommands = new BotCommandsList();
        $cachedCommands[] = new BotCommand('help', 'Показать справку');

        $this->cacheRepository->method('isBotCommandsCached')->willReturn(true);
        $this->cacheRepository->method('getBotCommands')->willReturn($cachedCommands);

        $this->botCommandsClient->expects($this->never())
            ->method('getBotCommands');

        $result = $this->service->getBotCommands(GetBotCommandsDTO::makeDTO());

        $this->assertInstanceOf(BotCommandsList::class, $result);
        $this->assertCount(1, $result);
    }

    public function testGetBotCommandsFetchesFromApiWhenCacheEmpty(): void
    {
        $apiCommands = [
            ['command' => 'help', 'description' => 'Показать справку'],
            ['command' => 'issue', 'description' => 'Сообщить о проблеме']
        ];

        $commandsList = new BotCommandsList();
        foreach ($apiCommands as $commandData) {
            $commandsList[] = new BotCommand(
                $commandData['command'],
                $commandData['description']
            );
        }

        $this->cacheRepository->method('isBotCommandsCached')->willReturn(false);
        $this->cacheRepository->method('getBotCommands')->willReturn(new BotCommandsList());

        $this->botCommandsClient->method('getBotCommands')->willReturn($commandsList);

        $this->cacheRepository->expects($this->once())
            ->method('setBotCommands')
            ->with($this->isInstanceOf(SetBotCommandsDTO::class));

        $result = $this->service->getBotCommands(GetBotCommandsDTO::makeDTO());

        $this->assertInstanceOf(BotCommandsList::class, $result);
        $this->assertCount(2, $result);
    }
}
