<?php

namespace App\Tests\Module\BotCommands\Factory;

use App\Module\BotCommands\Enum\BotCommandsEnum;
use App\Module\BotCommands\Factory\BotCommandHandlerFactory;
use App\Module\BotCommands\Handler\BotCommandHelpHandler;
use App\Module\BotCommands\Handler\BotCommandIssueHandler;
use App\Module\BotCommands\Infrastructure\Telegram\SendMessageClient;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\MessageBusInterface;

class BotCommandHandlerFactoryTest extends TestCase
{
    private BotCommandHandlerFactory $factory;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $messageBus = $this->createMock(MessageBusInterface::class);
        $sendMessageClient = $this->createMock(SendMessageClient::class);

        $this->factory = new BotCommandHandlerFactory($messageBus, $sendMessageClient);
    }

    public function testCreateHelpHandler(): void
    {
        $handler = $this->factory->createBotCommandHandler(BotCommandsEnum::HELP->value);

        $this->assertInstanceOf(BotCommandHelpHandler::class, $handler);
    }

    public function testCreateIssueHandler(): void
    {
        $handler = $this->factory->createBotCommandHandler(BotCommandsEnum::ISSUE->value);

        $this->assertInstanceOf(BotCommandIssueHandler::class, $handler);
    }

    public function testCreateUnknownHandlerReturnsNull(): void
    {
        $handler = $this->factory->createBotCommandHandler('/unknown');

        $this->assertNull($handler);
    }

    public function testCreateEmptyCommandReturnsNull(): void
    {
        $handler = $this->factory->createBotCommandHandler('');

        $this->assertNull($handler);
    }
}
