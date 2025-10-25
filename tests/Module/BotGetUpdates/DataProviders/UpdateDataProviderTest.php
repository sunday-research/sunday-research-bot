<?php

declare(strict_types=1);

namespace App\Tests\Module\BotGetUpdates\DataProviders;

use App\Tests\Module\BotGetUpdates\Fixtures\CommandUpdateFixture;
use App\Tests\Module\BotGetUpdates\Fixtures\EditedMessageUpdateFixture;
use App\Tests\Module\BotGetUpdates\Fixtures\ServerResponseFixture;
use App\Tests\Module\BotGetUpdates\Fixtures\TextUpdateFixture;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
final class UpdateDataProviderTest extends TestCase
{
    /**
     * This class only provides data for other tests.
     * No actual tests are performed here.
     */
    public function testDataProviderClass(): void
    {
        $this->assertTrue(true); // @phpstan-ignore-line
    }
    /**
     * @return array<string, array{testName: string, updateFactory: callable(): \Longman\TelegramBot\Entities\Update, shouldCreateSubscriber: bool, shouldCreateMessage: bool, shouldUpdateMessage?: bool}>
     */
    public static function messageUpdateProvider(TestCase $testCase): array
    {
        return [
            'command message' => [
                'testName' => 'testRunWithCommandMessage',
                'updateFactory' => fn() => (new CommandUpdateFixture($testCase))->create(),
                'shouldCreateSubscriber' => true,
                'shouldCreateMessage' => true,
            ],
            'text message' => [
                'testName' => 'testRunWithTextMessage',
                'updateFactory' => fn() => (new TextUpdateFixture($testCase))->create(),
                'shouldCreateSubscriber' => true,
                'shouldCreateMessage' => true,
            ],
            'edited message' => [
                'testName' => 'testRunWithEditedMessage',
                'updateFactory' => fn() => (new EditedMessageUpdateFixture($testCase))->create(),
                'shouldCreateSubscriber' => false, // Subscriber should already exist
                'shouldCreateMessage' => false,   // Message should already exist
                'shouldUpdateMessage' => true,
            ],
        ];
    }

    /**
     * @return array<string, array{testName: string, updateFactory: callable(): \Longman\TelegramBot\Entities\ServerResponse, shouldDoNothing: bool}>
     */
    public static function emptyUpdateProvider(TestCase $testCase): array
    {
        return [
            'empty updates' => [
                'testName' => 'testRunWithEmptyUpdates',
                'updateFactory' => fn() => (new ServerResponseFixture($testCase))->createEmpty(),
                'shouldDoNothing' => true,
            ],
        ];
    }

    /**
     * @return array<string, array{testName: string, updateFactory: callable(): \Longman\TelegramBot\Entities\Update, expectedCommand: string, expectedText: string}>
     */
    public static function commandVariationsProvider(TestCase $testCase): array
    {
        return [
            'issue command' => [
                'testName' => 'testRunWithIssueCommand',
                'updateFactory' => fn() => (new CommandUpdateFixture($testCase))->createWithCustomCommand('issue', '/issue@sunday_research_bot'),
                'expectedCommand' => 'issue',
                'expectedText' => '/issue@sunday_research_bot',
            ],
            'help command' => [
                'testName' => 'testRunWithHelpCommand',
                'updateFactory' => fn() => (new CommandUpdateFixture($testCase))->createWithCustomCommand('help', '/help'),
                'expectedCommand' => 'help',
                'expectedText' => '/help',
            ],
        ];
    }

    /**
     * @return array<string, array{testName: string, updateFactory: callable(): \Longman\TelegramBot\Entities\Update, expectedText: string}>
     */
    public static function textVariationsProvider(TestCase $testCase): array
    {
        return [
            'russian text' => [
                'testName' => 'testRunWithRussianText',
                'updateFactory' => fn() => (new TextUpdateFixture($testCase))->createWithCustomText('Привет, мир!'),
                'expectedText' => 'Привет, мир!',
            ],
            'english text' => [
                'testName' => 'testRunWithEnglishText',
                'updateFactory' => fn() => (new TextUpdateFixture($testCase))->createWithCustomText('Hello, world!'),
                'expectedText' => 'Hello, world!',
            ],
            'emoji text' => [
                'testName' => 'testRunWithEmojiText',
                'updateFactory' => fn() => (new TextUpdateFixture($testCase))->createWithCustomText('🚀 Test message'),
                'expectedText' => '🚀 Test message',
            ],
        ];
    }
}
