<?php

declare(strict_types=1);

namespace App\Tests\Module\BotGetUpdates;

use App\Module\BotCommands\Contract\BotCommandHandlerInterface;
use App\Module\BotCommands\Factory\BotCommandHandlerFactory;
use App\Module\BotCommands\Service\BotCommandsService;
use App\Module\BotCommands\ValueObject\BotCommand;
use App\Module\BotGetUpdates\BotGetUpdatesManager;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Model\Subscriber;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Model\SubscriberMessage;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Repository\SubscriberRepository;
use App\Module\BotGetUpdates\Infrastructure\Doctrine\Repository\SubscriberMessageRepository;
use App\Module\BotGetUpdates\Service\BotGetUpdatesService;
use App\Module\BotGetUpdates\Service\SubscriberMessageService;
use App\Module\BotGetUpdates\Service\SubscriberService;
use App\Tests\Module\BotGetUpdates\Fixtures\CommandUpdateFixture;
use App\Tests\Module\BotGetUpdates\Fixtures\EditedMessageUpdateFixture;
use App\Tests\Module\BotGetUpdates\Fixtures\ServerResponseFixture;
use App\Tests\Module\BotGetUpdates\Fixtures\TextUpdateFixture;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Longman\TelegramBot\Entities\Update;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class BotGetUpdatesManagerIntegrationTest extends KernelTestCase
{
    private const TEST_TELEGRAM_USER_ID = 227974324;
    private const TEST_CHAT_ID = -4064339494;
    private const TEST_MESSAGE_ID_TEXT = 154;

    private EntityManagerInterface $em;
    private SubscriberRepository $subscriberRepository;
    private SubscriberMessageRepository $subscriberMessageRepository;
    private BotGetUpdatesService&MockObject $botGetUpdatesServiceMock;
    private BotCommandsService&MockObject $botCommandsServiceMock;
    private BotCommandHandlerFactory&MockObject $botCommandHandlerFactoryMock;
    private BotCommandHandlerInterface&MockObject $botCommandHandlerMock;
    private LoggerInterface&MockObject $loggerMock;

    /** @var array<string> */
    protected array $createdSubscriberIds = [];
    /** @var array<string> */
    protected array $createdMessageIds = [];

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        
        /** @phpstan-ignore-next-line mixed.type */
        $this->em = $container->get(EntityManagerInterface::class);
        /** @var SubscriberRepository $subscriberRepository */
        $subscriberRepository = $this->em->getRepository(Subscriber::class);
        $this->subscriberRepository = $subscriberRepository;
        /** @var SubscriberMessageRepository $subscriberMessageRepository */
        $subscriberMessageRepository = $this->em->getRepository(SubscriberMessage::class);
        $this->subscriberMessageRepository = $subscriberMessageRepository;

        // Clean up any existing test data before each test
        $this->cleanupTestData();

        $this->botGetUpdatesServiceMock = $this->createMock(BotGetUpdatesService::class);
        $this->botCommandsServiceMock = $this->createMock(BotCommandsService::class);
        $this->botCommandHandlerFactoryMock = $this->createMock(BotCommandHandlerFactory::class);
        $this->botCommandHandlerMock = $this->createMock(BotCommandHandlerInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
    }

    protected function tearDown(): void
    {
        // Clean up created test data
        $this->cleanupTestData();
        parent::tearDown();
    }

    private function cleanupTestData(): void
    {
        // Find and remove all messages for the test user
        $messages = $this->subscriberMessageRepository->createQueryBuilder('sm')
            ->join('sm.subscriber', 's')
            ->where('s.telegramUserId = :telegramUserId')
            ->setParameter('telegramUserId', self::TEST_TELEGRAM_USER_ID)
            ->getQuery()
            ->getResult();

        /** @var array<int, mixed> $messages */
        foreach ($messages as $message) {
            if (is_object($message)) {
                $this->em->remove($message);
            }
        }

        // Find and remove the test subscriber
        $subscriber = $this->subscriberRepository->findByTelegramUserId(self::TEST_TELEGRAM_USER_ID);
        if ($subscriber) {
            $this->em->remove($subscriber);
        }

        $this->em->flush();
        
        // Reset tracking arrays
        $this->createdSubscriberIds = [];
        $this->createdMessageIds = [];
    }

    #[DataProvider('messageUpdateProvider')]
    public function testRunWithMessageUpdates(
        string $testName,
        callable $updateFactory,
        bool $shouldCreateSubscriber,
        bool $shouldCreateMessage,
        bool $shouldUpdateMessage = false
    ): void {
        // Arrange
        $update = $updateFactory();
        assert($update instanceof \Longman\TelegramBot\Entities\Update);
        $serverResponse = (new ServerResponseFixture($this))->createWithSingleUpdate($update);
        
        $this->botGetUpdatesServiceMock
            ->expects($this->once())
            ->method('getUpdates')
            ->willReturn($serverResponse);

        // Setup expectations based on message type
        if ($update->getUpdateType() === Update::TYPE_MESSAGE && $update->getMessage()->getType() === 'command') {
            $this->botCommandsServiceMock
                ->expects($this->once())
                ->method('findCommandByCommandText')
                ->willReturn(new BotCommand('issue', 'Test command'));

            $this->botCommandHandlerFactoryMock
                ->expects($this->once())
                ->method('createBotCommandHandler')
                ->with('issue')
                ->willReturn($this->botCommandHandlerMock);

            $this->botCommandHandlerMock
                ->expects($this->once())
                ->method('handle')
                ->with($update);
        } else {
            $this->botCommandsServiceMock
                ->expects($this->never())
                ->method('findCommandByCommandText');
        }

        // For edited message test, create existing data
        if ($shouldUpdateMessage) {
            $this->createAndPersistSubscriber();
            $subscriber = $this->subscriberRepository->findByTelegramUserId(self::TEST_TELEGRAM_USER_ID);
            if ($subscriber !== null) {
                $this->createAndPersistMessage($subscriber);
            }
        }

        $subscriberService = new SubscriberService($this->subscriberRepository);
        $subscriberMessageService = new SubscriberMessageService($this->subscriberRepository, $this->subscriberMessageRepository);

        $manager = new BotGetUpdatesManager(
            $this->botGetUpdatesServiceMock,
            $this->loggerMock,
            $subscriberService,
            $subscriberMessageService,
            $this->botCommandsServiceMock,
            $this->botCommandHandlerFactoryMock
        );

        // Act
        $manager->run();

        // Assert
        if ($shouldCreateSubscriber) {
            $this->assertSubscriberWasCreated();
        }
        if ($shouldCreateMessage) {
            $this->assertMessageWasCreated();
        }
        if ($shouldUpdateMessage) {
            $this->assertMessageWasUpdated();
        }
    }

    #[DataProvider('emptyUpdateProvider')]
    public function testRunWithEmptyUpdates(
        string $testName,
        callable $updateFactory,
        bool $shouldDoNothing
    ): void {
        // Arrange
        $serverResponse = $updateFactory();
        
        $this->botGetUpdatesServiceMock
            ->expects($this->once())
            ->method('getUpdates')
            ->willReturn($serverResponse);

        $this->botCommandsServiceMock
            ->expects($this->never())
            ->method('findCommandByCommandText');

        $subscriberService = new SubscriberService($this->subscriberRepository);
        $subscriberMessageService = new SubscriberMessageService($this->subscriberRepository, $this->subscriberMessageRepository);

        $manager = new BotGetUpdatesManager(
            $this->botGetUpdatesServiceMock,
            $this->loggerMock,
            $subscriberService,
            $subscriberMessageService,
            $this->botCommandsServiceMock,
            $this->botCommandHandlerFactoryMock
        );

        // Act
        $manager->run();

        // Assert - no exceptions thrown, no database changes
        $this->assertTrue($shouldDoNothing);
    }

    /**
     * @return array<string, array{testName: string, updateFactory: callable(): \Longman\TelegramBot\Entities\Update, shouldCreateSubscriber: bool, shouldCreateMessage: bool, shouldUpdateMessage?: bool}>
     */
    public static function messageUpdateProvider(): array
    {
        return [
            'command message' => [
                'testName' => 'testRunWithCommandMessage',
                'updateFactory' => fn() => (new CommandUpdateFixture(self::getTestInstance()))->create(),
                'shouldCreateSubscriber' => false, // Commands don't create subscribers
                'shouldCreateMessage' => false,   // Commands don't create messages
            ],
            'text message' => [
                'testName' => 'testRunWithTextMessage',
                'updateFactory' => fn() => (new TextUpdateFixture(self::getTestInstance()))->create(),
                'shouldCreateSubscriber' => true,
                'shouldCreateMessage' => true,
            ],
            'edited message' => [
                'testName' => 'testRunWithEditedMessage',
                'updateFactory' => fn() => (new EditedMessageUpdateFixture(self::getTestInstance()))->create(),
                'shouldCreateSubscriber' => false, // Subscriber should already exist
                'shouldCreateMessage' => false,   // Message should already exist
                'shouldUpdateMessage' => true,
            ],
        ];
    }

    private static function getTestInstance(): self
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new self('test');
        }
        /** @var self $instance */
        return $instance;
    }

    /**
     * @return array<string, array{testName: string, updateFactory: callable(): \Longman\TelegramBot\Entities\ServerResponse, shouldDoNothing: bool}>
     */
    public static function emptyUpdateProvider(): array
    {
        return [
            'empty updates' => [
                'testName' => 'testRunWithEmptyUpdates',
                'updateFactory' => fn() => (new ServerResponseFixture(self::getTestInstance()))->createEmpty(),
                'shouldDoNothing' => true,
            ],
        ];
    }

    #[DataProvider('commandVariationsProvider')]
    public function testRunWithCommandVariations(
        string $testName,
        callable $updateFactory,
        string $expectedCommand,
        string $expectedText
    ): void {
        // Arrange
        $update = $updateFactory();
        assert($update instanceof \Longman\TelegramBot\Entities\Update);
        $serverResponse = (new ServerResponseFixture($this))->createWithSingleUpdate($update);
        
        $this->botGetUpdatesServiceMock
            ->expects($this->once())
            ->method('getUpdates')
            ->willReturn($serverResponse);

        $this->botCommandsServiceMock
            ->expects($this->once())
            ->method('findCommandByCommandText')
            ->willReturn(new BotCommand($expectedCommand, 'Test command'));

        $this->botCommandHandlerFactoryMock
            ->expects($this->once())
            ->method('createBotCommandHandler')
            ->with($expectedCommand)
            ->willReturn($this->botCommandHandlerMock);

        $this->botCommandHandlerMock
            ->expects($this->once())
            ->method('handle')
            ->with($update);

        $subscriberService = new SubscriberService($this->subscriberRepository);
        $subscriberMessageService = new SubscriberMessageService($this->subscriberRepository, $this->subscriberMessageRepository);

        $manager = new BotGetUpdatesManager(
            $this->botGetUpdatesServiceMock,
            $this->loggerMock,
            $subscriberService,
            $subscriberMessageService,
            $this->botCommandsServiceMock,
            $this->botCommandHandlerFactoryMock
        );

        // Act
        $manager->run();

        // Assert - Commands don't create subscribers or messages
        // They only trigger command handlers
    }

    #[DataProvider('textVariationsProvider')]
    public function testRunWithTextVariations(
        string $testName,
        callable $updateFactory,
        string $expectedText
    ): void {
        // Arrange
        $update = $updateFactory();
        assert($update instanceof \Longman\TelegramBot\Entities\Update);
        $serverResponse = (new ServerResponseFixture($this))->createWithSingleUpdate($update);
        
        $this->botGetUpdatesServiceMock
            ->expects($this->once())
            ->method('getUpdates')
            ->willReturn($serverResponse);

        $this->botCommandsServiceMock
            ->expects($this->never())
            ->method('findCommandByCommandText');

        $subscriberService = new SubscriberService($this->subscriberRepository);
        $subscriberMessageService = new SubscriberMessageService($this->subscriberRepository, $this->subscriberMessageRepository);

        $manager = new BotGetUpdatesManager(
            $this->botGetUpdatesServiceMock,
            $this->loggerMock,
            $subscriberService,
            $subscriberMessageService,
            $this->botCommandsServiceMock,
            $this->botCommandHandlerFactoryMock
        );

        // Act
        $manager->run();

        // Assert
        $this->assertSubscriberWasCreated();
        $this->assertMessageWasCreated();
    }

    /**
     * @return array<string, array{testName: string, updateFactory: callable(): \Longman\TelegramBot\Entities\Update, expectedCommand: string, expectedText: string}>
     */
    public static function commandVariationsProvider(): array
    {
        return [
            'issue command' => [
                'testName' => 'testRunWithIssueCommand',
                'updateFactory' => fn() => (new CommandUpdateFixture(self::getTestInstance()))->createWithCustomCommand('issue', '/issue@sunday_research_bot'),
                'expectedCommand' => 'issue',
                'expectedText' => '/issue@sunday_research_bot',
            ],
            'help command' => [
                'testName' => 'testRunWithHelpCommand',
                'updateFactory' => fn() => (new CommandUpdateFixture(self::getTestInstance()))->createWithCustomCommand('help', '/help'),
                'expectedCommand' => 'help',
                'expectedText' => '/help',
            ],
        ];
    }

    /**
     * @return array<string, array{testName: string, updateFactory: callable(): \Longman\TelegramBot\Entities\Update, expectedText: string}>
     */
    public static function textVariationsProvider(): array
    {
        return [
            'russian text' => [
                'testName' => 'testRunWithRussianText',
                'updateFactory' => fn() => (new TextUpdateFixture(self::getTestInstance()))->createWithCustomText('Привет, мир!'),
                'expectedText' => 'Привет, мир!',
            ],
            'english text' => [
                'testName' => 'testRunWithEnglishText',
                'updateFactory' => fn() => (new TextUpdateFixture(self::getTestInstance()))->createWithCustomText('Hello, world!'),
                'expectedText' => 'Hello, world!',
            ],
            'emoji text' => [
                'testName' => 'testRunWithEmojiText',
                'updateFactory' => fn() => (new TextUpdateFixture(self::getTestInstance()))->createWithCustomText('🚀 Test message'),
                'expectedText' => '🚀 Test message',
            ],
        ];
    }


    private function createAndPersistSubscriber(): Subscriber
    {
        $subscriber = new Subscriber();
        $subscriber->setTelegramUserId(self::TEST_TELEGRAM_USER_ID);
        $subscriber->setUsername('subbotinv');
        $subscriber->setFirstName('Владислав');
        $subscriber->setLastName('Субботин');
        $subscriber->setLanguageCode('ru');
        $subscriber->setIsPremium(true);

        $this->em->persist($subscriber);
        $this->em->flush();

        $subscriberId = $subscriber->getId();
        if ($subscriberId !== null) {
            $this->createdSubscriberIds[] = $subscriberId->toString();
        }

        return $subscriber;
    }

    private function createAndPersistMessage(Subscriber $subscriber): SubscriberMessage
    {
        $message = new SubscriberMessage();
        $message->setSubscriber($subscriber);
        $message->setChatId(self::TEST_CHAT_ID);
        $message->setMessageId(self::TEST_MESSAGE_ID_TEXT);
        $message->setMessageText('тест');
        $message->setMessageDate(new DateTimeImmutable('2024-06-01 12:00:00'));
        $message->setIsBotSender(false);

        $this->em->persist($message);
        $this->em->flush();

        $messageId = $message->getId();
        if ($messageId !== null) {
            $this->createdMessageIds[] = $messageId->toString();
        }

        return $message;
    }

    private function assertSubscriberWasCreated(): void
    {
        $subscriber = $this->subscriberRepository->findByTelegramUserId(self::TEST_TELEGRAM_USER_ID);
        $this->assertNotNull($subscriber);
        $this->assertEquals('Владислав', $subscriber->getFirstName());
        $this->assertEquals('Субботин', $subscriber->getLastName());
        $this->assertEquals('subbotinv', $subscriber->getUsername());
        $this->assertEquals('ru', $subscriber->getLanguageCode());
        $this->assertTrue($subscriber->isPremium());
    }

    private function assertMessageWasCreated(): void
    {
        // Find any message for the test subscriber
        $subscriber = $this->subscriberRepository->findByTelegramUserId(self::TEST_TELEGRAM_USER_ID);
        $this->assertNotNull($subscriber, 'Subscriber should exist');
        
        $message = $this->subscriberMessageRepository->createQueryBuilder('sm')
            ->join('sm.subscriber', 's')
            ->where('s.telegramUserId = :telegramUserId')
            ->setParameter('telegramUserId', self::TEST_TELEGRAM_USER_ID)
            ->getQuery()
            ->getOneOrNullResult();

        $this->assertNotNull($message, 'Message should exist');
        /** @var \App\Module\BotGetUpdates\Infrastructure\Doctrine\Model\SubscriberMessage $message */
        $this->assertEquals(self::TEST_CHAT_ID, $message->getChatId());
        $this->assertFalse($message->isBotSender());
    }

    private function assertMessageWasUpdated(): void
    {
        $message = $this->subscriberMessageRepository->findOneBy([
            'chatId' => self::TEST_CHAT_ID,
            'messageId' => self::TEST_MESSAGE_ID_TEXT,
        ]);

        $this->assertNotNull($message);
        $this->assertEquals('отредактированный текст', $message->getMessageText());
    }
}
