<?php

namespace App\Tests\Module\BotCommands\MessageHandler;

use App\Module\BotCommands\DTO\SendTextMessageDTO;
use App\Module\BotCommands\Infrastructure\Telegram\SendMessageClient;
use App\Module\BotCommands\Message\BotCommandHelpMessage;
use App\Module\BotCommands\MessageHandler\BotCommandHelpMessageHandler;
use App\Module\BotCommands\Service\BotCommandsService;
use App\Module\BotCommands\ValueObject\BotCommand;
use App\Module\BotCommands\ValueObject\BotCommandsList;
use Longman\TelegramBot\Entities\Chat;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class BotCommandHelpMessageHandlerTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testHandlerConstructor(): void
    {
        $handler = new BotCommandHelpMessageHandler(
            $this->createMock(SendMessageClient::class),
            $this->createMock(BotCommandsService::class)
        );

        $this->assertInstanceOf(BotCommandHelpMessageHandler::class, $handler);
    }

    /**
     * @throws Exception
     */
    public function testInvokeSendsHelpMessage(): void
    {
        $sendMessageClient = $this->createMock(SendMessageClient::class);
        $botCommandsService = $this->createMock(BotCommandsService::class);

        $handler = new BotCommandHelpMessageHandler($sendMessageClient, $botCommandsService);

        $chat = new Chat(['id' => 12345, 'type' => 'private']);

        $helpMessage = $this->createMock(BotCommandHelpMessage::class);
        $helpMessage->method('getChat')->willReturn($chat);

        $commandsList = new BotCommandsList();
        $commandsList[] = new BotCommand('help', 'Показать справку');
        $commandsList[] = new BotCommand('issue', 'Предложить улучшение');

        $botCommandsService->method('getBotCommands')
            ->willReturn($commandsList);

        $sendMessageClient->expects($this->once())
            ->method('sendTextMessage')
            ->with($this->callback(function (SendTextMessageDTO $dto) {
                $text = $dto->getText();
                return $dto->getChatId() === '12345'
                    && str_contains($text, 'Sunday Research Bot')
                    && str_contains($text, 'Доступные команды');
            }));

        $handler->__invoke($helpMessage);
    }
}
