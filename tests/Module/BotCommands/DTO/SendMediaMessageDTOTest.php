<?php

declare(strict_types=1);

namespace App\Tests\Module\BotCommands\DTO;

use App\Module\BotCommands\DTO\SendMediaMessageDTO;
use PHPUnit\Framework\TestCase;

final class SendMediaMessageDTOTest extends TestCase
{
    /**
     * @dataProvider mediaTypeDetectionProvider
     */
    public function testIsFileIdDetection(string $media, bool $expectedIsFileId, string $description): void
    {
        $dto = SendMediaMessageDTO::makeDTO(
            '123456',
            $media,
            'Test caption',
            null,
            null,
            'photo'
        );

        $this->assertEquals(
            $expectedIsFileId,
            $dto->isFileId(),
            sprintf('Failed for: %s', $description)
        );
    }

    /**
     * @return array<string, array{string, bool, string}>
     */
    public function mediaTypeDetectionProvider(): array
    {
        return [
            'external http link' => [
                'http://example.com/image.jpg',
                false,
                'External HTTP link should not be detected as file_id'
            ],
            'external https link' => [
                'https://media.giphy.com/media/example.gif',
                false,
                'External HTTPS link should not be detected as file_id'
            ],
            'local file path' => [
                'assets/media/images/test-image.jpg',
                false,
                'Local file path should not be detected as file_id'
            ],
            'relative file path' => [
                './images/logo.png',
                false,
                'Relative file path should not be detected as file_id'
            ],
            'absolute file path' => [
                '/var/www/assets/image.jpg',
                false,
                'Absolute file path should not be detected as file_id'
            ],
            'telegram file_id format' => [
                'BQACAgIAAxkBAAIBY2WXvXqXvXqXvXqXvXqXvXqXvXqX',
                true,
                'Telegram file_id format should be detected as file_id'
            ],
            'telegram file_id with underscores' => [
                'BQACAgIAAxkBAAIBY2WX_vXqXvXqXvXqXvXqXvXqXvXqX',
                true,
                'Telegram file_id with underscores should be detected as file_id'
            ],
            'telegram file_id with hyphens' => [
                'BQACAgIAAxkBAAIBY2WX-vXqXvXqXvXqXvXqXvXqXvXqX',
                true,
                'Telegram file_id with hyphens should be detected as file_id'
            ],
            'short string not file_id' => [
                'short',
                false,
                'Short string should not be detected as file_id'
            ],
            'empty string' => [
                '',
                false,
                'Empty string should not be detected as file_id'
            ],
        ];
    }

    public function testMakeDTO(): void
    {
        $dto = SendMediaMessageDTO::makeDTO(
            '123456',
            'test-media.jpg',
            'Test caption',
            'MarkdownV2',
            null,
            'photo'
        );

        $this->assertEquals('123456', $dto->getChatId());
        $this->assertEquals('test-media.jpg', $dto->getMedia());
        $this->assertEquals('Test caption', $dto->getCaption());
        $this->assertEquals('MarkdownV2', $dto->getParseMode());
        $this->assertEquals('photo', $dto->getMediaType());
        $this->assertNull($dto->getReplyMarkup());
    }

    public function testToArray(): void
    {
        $dto = SendMediaMessageDTO::makeDTO(
            '123456',
            'test-image.jpg',
            'Test caption',
            'MarkdownV2',
            null,
            'photo'
        );

        $expected = [
            'chat_id' => '123456',
            'photo' => 'test-image.jpg',
            'caption' => 'Test caption',
            'parse_mode' => 'MarkdownV2',
            'reply_markup' => null,
        ];

        $this->assertEquals($expected, $dto->toArray());
    }
} 